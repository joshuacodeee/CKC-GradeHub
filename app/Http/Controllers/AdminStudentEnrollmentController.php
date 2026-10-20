<?php

namespace App\Http\Controllers;

use App\Models\ClassOfferingStudent;
use App\Models\Curriculum;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use App\Services\EnrollmentSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminStudentEnrollmentController extends Controller
{
    /**
     * The manage-enrollment page. Shows the student's profile, the set of
     * class offerings they are currently in, and the curriculum checklist
     * for their (course, year, semester) — with status badges for
     * "passed / currently enrolled / missing".
     */
    public function show(User $student)
    {
        abort_unless($student->role === 'student', 404);

        $schoolYear = app(EnrollmentSyncService::class)->currentSchoolYear();

        $curriculum = $schoolYear
            ? Curriculum::findFor((string) $student->course, (string) $student->year_level, $schoolYear->semester, $schoolYear->id)
            : null;

        $checklist = $this->buildChecklist($student, $curriculum, $schoolYear);

        // The "currently enrolled" list comes from the class_offering_student
        // pivot table directly. Filtering by school year uses a qualified
        // column reference; using `wherePivot` here would force us to also
        // declare the pivot table alias, which is awkward.
        $offeringIds = ClassOfferingStudent::where('student_id', $student->id)
            ->when($schoolYear, fn ($q) => $q->where('class_offering_student.school_year_id', $schoolYear->id))
            ->pluck('teacher_subject_id');

        $currentOfferings = TeacherSubject::whereIn('id', $offeringIds)
            ->with(['subject', 'section.schoolYear', 'schoolYear'])
            ->get()
            ->sortBy(fn ($o) => $o->subject?->code ?? '')
            ->values();

        return view('admin.student-enrollment', [
            'student' => $student,
            'schoolYear' => $schoolYear,
            'curriculum' => $curriculum,
            'checklist' => $checklist,
            'currentOfferings' => $currentOfferings,
        ]);
    }

    /**
     * Build a per-subject checklist for the curriculum, annotated with
     * each subject's status from this student's perspective.
     *
     * @return array<int, array{subject:Subject, status:string, note:?string, offering:?TeacherSubject}>
     */
    protected function buildChecklist(User $student, ?Curriculum $curriculum, ?SchoolYear $schoolYear): array
    {
        if (! $curriculum) {
            return [];
        }

        $passedSubjectIds = Grade::where('student_id', $student->id)
            ->where('status', 'submitted')
            ->where('remarks', 'Passed')
            ->pluck('subject_id')
            ->unique()
            ->all();

        $explicitSubjectIds = ClassOfferingStudent::where('student_id', $student->id)
            ->when($schoolYear, fn ($q) => $q->where('class_offering_student.school_year_id', $schoolYear->id))
            ->join('teacher_subjects', 'teacher_subjects.id', '=', 'class_offering_student.teacher_subject_id')
            ->pluck('teacher_subjects.subject_id')
            ->unique()
            ->all();

        $items = [];
        foreach ($curriculum->subjects()->get() as $subject) {
            if (in_array($subject->id, $passedSubjectIds, true)) {
                $items[] = [
                    'subject' => $subject,
                    'status' => 'passed',
                    'note' => 'Already passed',
                    'offering' => null,
                ];
                continue;
            }
            $offering = null;
            if (in_array($subject->id, $explicitSubjectIds, true)) {
                $offering = TeacherSubject::whereIn('id', ClassOfferingStudent::where('student_id', $student->id)
                    ->when($schoolYear, fn ($q) => $q->where('class_offering_student.school_year_id', $schoolYear->id))
                    ->pluck('teacher_subject_id'))
                    ->where('subject_id', $subject->id)
                    ->first();
            }
            $items[] = [
                'subject' => $subject,
                'status' => $offering ? 'enrolled' : 'missing',
                'note' => $offering
                    ? 'Enrolled in ' . $offering->section?->name . ' · ' . $offering->section?->year_level
                    : 'Not yet enrolled',
                'offering' => $offering,
            ];
        }

        return $items;
    }

    /**
     * Toggle the is_irregular flag on a student and re-sync their
     * class-offering memberships against the new mode.
     */
    public function toggleIrregular(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $student->is_irregular = ! $student->is_irregular;
        $student->save();

        $service = app(EnrollmentSyncService::class);
        $schoolYear = $service->currentSchoolYear();
        $result = $service->syncForStudent($student, $schoolYear);

        return redirect()
            ->route('admin.student-enrollment', $student)
            ->with('success', $student->is_irregular
                ? 'Student marked as irregular. Auto-enrollment paused.'
                : "Student marked as regular. Synced: +{$result['added_offerings']} offerings, -{$result['removed_offerings']} removed.");
    }

    /**
     * Force a re-sync of the student's class-offering memberships from
     * the curriculum. Useful for regular students whose profile was
     * edited, or for any time an admin wants to start clean.
     */
    public function sync(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $service = app(EnrollmentSyncService::class);
        $schoolYear = $service->currentSchoolYear();
        $result = $service->syncForStudent($student, $schoolYear);

        return redirect()
            ->route('admin.student-enrollment', $student)
            ->with('success', "Synced: +{$result['added_offerings']} added, -{$result['removed_offerings']} removed.");
    }

    /**
     * Assign a student to a specific class offering. Used by the
     * assign-from-checklist modal. Creates a ClassOfferingStudent row
     * and a draft Grade row.
     */
    public function assign(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $data = $request->validate([
            'teacher_subject_id' => ['required', 'exists:teacher_subjects,id'],
        ]);

        $offering = TeacherSubject::findOrFail($data['teacher_subject_id']);
        $assigned = app(EnrollmentSyncService::class)
            ->assignToOffering($student, $offering, Auth::user());

        if (! $assigned) {
            return back()->with('error', 'Student is already enrolled in this subject for that school year and semester.');
        }

        return redirect()
            ->route('admin.student-enrollment', $student)
            ->with('success', 'Student assigned to ' . ($offering->subject?->code ?? 'class') . ' · ' . ($offering->section?->name ?? ''));
    }

    /**
     * Remove a student's explicit assignment to a class offering.
     * For regulars this also drops the row only if the offering is not
     * part of the current curriculum (i.e. the row came from an explicit
     * assign, not from auto-sync).
     */
    public function unassign(Request $request, User $student, TeacherSubject $offering)
    {
        abort_unless($student->role === 'student', 404);

        $row = ClassOfferingStudent::where('teacher_subject_id', $offering->id)
            ->where('student_id', $student->id)
            ->first();
        if (! $row) {
            return back()->with('error', 'No assignment to remove.');
        }

        // If a submitted grade exists, keep the row (history) but warn.
        $hasSubmittedGrade = Grade::where('teacher_assignment_id', $offering->id)
            ->where('student_id', $student->id)
            ->where('status', 'submitted')
            ->exists();
        if ($hasSubmittedGrade) {
            return back()->with('error', 'Cannot remove: this student already has a submitted grade in this class. Their grade history will be preserved.');
        }

        $row->delete();

        return redirect()
            ->route('admin.student-enrollment', $student)
            ->with('success', 'Assignment removed.');
    }

    /**
     * Look up the class offerings available for a subject — used by the
     * AJAX-style picker in the assign modal. Returns JSON.
     */
    public function offeringsForSubject(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        $service = app(EnrollmentSyncService::class);
        $schoolYear = $service->currentSchoolYear();

        $offerings = TeacherSubject::where('subject_id', $data['subject_id'])
            ->where('status', 'active')
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with(['teacher', 'section'])
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'class_code' => $o->class_code,
                'teacher_name' => $o->teacher?->name,
                'section_name' => $o->section?->name,
                'year_level' => $o->section?->year_level,
                'block' => $o->section?->block,
                'course' => $o->section?->course,
            ])
            ->values();

        return response()->json(['offerings' => $offerings]);
    }
}
