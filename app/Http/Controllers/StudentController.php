<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Services\EnrollmentSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * The current (or latest) active school year.
     */
    protected function currentSchoolYear()
    {
        return SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::where('is_active', true)->latest()->first()
            ?? SchoolYear::latest()->first();
    }

    /**
     * Compute GWA from an Eloquent collection of Grade models.
     */
    protected function computeGwa($grades): ?float
    {
        $weighted = 0;
        $totalUnits = 0;

        foreach ($grades as $grade) {
            if ($grade->final_grade !== null && $grade->subject) {
                $weighted += $grade->final_grade * $grade->subject->units;
                $totalUnits += $grade->subject->units;
            }
        }

        return $totalUnits > 0 ? round($weighted / $totalUnits, 2) : null;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        // Auto-enroll the student into a matching section if they aren't
        // enrolled yet. Idempotent — runs every page load so existing
        // students get linked as soon as a section is created. Also syncs
        // the per-class-offering memberships for regular students.
        $this->ensureEnrollment($user, $schoolYear);

        $enrollments = Enrollment::where('student_id', $user->id)
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('status', 'enrolled')
            ->with('section')
            ->get();

        $section = $enrollments->first()?->section;

        // Subjects the student should see come from two sources:
        //   a) explicit class_offering_student rows (the new per-class
        //      membership; covers both regular and irregular students)
        //   b) legacy: every active teacher_subjects row in the section
        //      (covers any pre-migration student that doesn't yet have
        //      explicit rows). Skipped for irregular students — they
        //      only see what the registrar explicitly assigned.
        $explicitSubjectIds = \App\Models\ClassOfferingStudent::where('student_id', $user->id)
            ->when($schoolYear, fn ($q) => $q->where('class_offering_student.school_year_id', $schoolYear->id))
            ->join('teacher_subjects', 'teacher_subjects.id', '=', 'class_offering_student.teacher_subject_id')
            ->pluck('teacher_subjects.subject_id');

        $subjectIds = $explicitSubjectIds->unique();

        if (! $user->is_irregular) {
            $legacyQuery = \App\Models\TeacherSubject::where('section_id', optional($section)->id)
                ->where('status', 'active');
            if ($schoolYear) {
                $legacyQuery->where(function ($q) use ($schoolYear) {
                    $q->where('school_year_id', $schoolYear->id)
                        ->orWhereNull('school_year_id');
                });
            }
            $legacySubjectIds = $legacyQuery->pluck('subject_id');
            $subjectIds = $subjectIds->merge($legacySubjectIds)->unique()->values();
        }

        $enrolledSubjects = Subject::whereIn('id', $subjectIds)->get();
        $totalUnits = $enrolledSubjects->sum('units');

        $grades = Grade::where('student_id', $user->id)
            ->whereIn('teacher_assignment_id',
                \App\Models\TeacherSubject::whereIn('subject_id', $subjectIds)->pluck('id'))
            ->with(['teacherAssignment.subject', 'subject'])
            ->get();

        $gwa = $this->computeGwa($grades);

        $standing = match (true) {
            $gwa === null  => 'No grades yet',
            $gwa >= 90     => "Dean's list",
            $gwa >= 85     => "Very good standing",
            $gwa >= 80     => "Good standing",
            $gwa >= 75     => 'Passing',
            default        => 'Needs improvement',
        };
        $standingTone = match (true) {
            $gwa === null  => 'info',
            $gwa >= 90     => 'success',
            $gwa >= 75     => 'info',
            default        => 'danger',
        };

        $latestGrade = $grades
            ->sortByDesc(fn ($g) => $g->updated_at ?? now()->subYears(10))
            ->first();

        $enrolledCount = (int) $enrolledSubjects->count();
        $gradedCount = $grades->whereNotNull('final_grade')->count();

        return view('student.dashboard', [
            'user' => $user,
            'section' => $section,
            'enrolledSubjects' => $enrolledSubjects,
            'totalUnits' => $totalUnits,
            'enrolledCount' => $enrolledCount,
            'gradedCount' => $gradedCount,
            'grades' => $grades,
            'gwa' => $gwa,
            'standing' => $standing,
            'standingTone' => $standingTone,
            'latestGrade' => $latestGrade,
            'schoolYear' => $schoolYear,
        ]);
    }

    /**
     * If the student is not currently enrolled, find a section matching
     * their course / year_level / block in the current school year and
     * create an enrollment. Skips silently if no section matches. Block
     * matching is exact: a student with block "B" only matches a section
     * with block "B".
     *
     * Flow:
     *   • Regular students get their section row synced AND get every
     *     curriculum offering auto-enrolled.
     *   • Irregular students get their section row synced (so they remain
     *     a member of a section for record-keeping), but the per-class
     *     offering sync is skipped — the registrar is the only one who
     *     adds them to specific class offerings via "Manage Enrollment".
     */
    protected function ensureEnrollment($user, $schoolYear): void
    {
        $service = app(EnrollmentSyncService::class);
        $service->syncSectionEnrollment($user, $schoolYear);

        if ($user->is_irregular) {
            return;
        }
        $service->addOfferingsFromCurriculum($user, $schoolYear);
    }

    public function grades()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $enrollments = Enrollment::where('student_id', $user->id)
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->get();

        $gradeRecords = Grade::where('student_id', $user->id)
            ->where('status', 'submitted')
            ->with([
                'teacherAssignment.subject',
                'teacherAssignment.teacher',
                'subject',
            ])
            ->get();

        $gwa = $this->computeGwa($gradeRecords);

        return view('student.grades', [
            'grades' => $gradeRecords,
            'gwa' => $gwa,
            'enrollments' => $enrollments,
            'schoolYear' => $schoolYear,
        ]);
    }

    public function subjects()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $this->ensureEnrollment($user, $schoolYear);

        // Source 1: explicit class-offering memberships (covers irregular
        // students and any explicitly-assigned offerings).
        $explicitOfferings = \App\Models\TeacherSubject::whereIn('id', function ($q) use ($user, $schoolYear) {
                $q->select('teacher_subject_id')
                    ->from('class_offering_student')
                    ->where('student_id', $user->id)
                    ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id));
            })
            ->with('subject')
            ->get()
            ->keyBy('subject_id');

        // Source 2: legacy — all active offerings in any section the
        // student is currently enrolled in. **Skipped for irregular
        // students** — they should only see what the registrar explicitly
        // assigned via class_offering_student, never the whole section.
        $offerings = $explicitOfferings;
        if (! $user->is_irregular) {
            $sectionIds = Enrollment::where('student_id', $user->id)
                ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
                ->pluck('section_id');

            $legacyOfferings = \App\Models\TeacherSubject::whereIn('section_id', $sectionIds)
                ->when($schoolYear, function ($q) use ($schoolYear) {
                    $q->where(function ($q) use ($schoolYear) {
                        $q->where('school_year_id', $schoolYear->id)
                            ->orWhereNull('school_year_id');
                    });
                })
                ->where('status', 'active')
                ->get()
                ->keyBy('subject_id');

            $offerings = $offerings->merge($legacyOfferings)->unique('id');
        }

        $subjectIds = $offerings->pluck('subject_id')->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();
        $totalUnits = $subjects->sum('units');

        // Map each subject to the (first) offering that places it on this
        // student's list. The view uses it to show the class code.
        $subjectOfferings = $subjects->mapWithKeys(function ($subject) use ($offerings) {
            $match = $offerings->firstWhere('subject_id', $subject->id);
            return [$subject->id => $match];
        });

        return view('student.subjects', [
            'subjects' => $subjects,
            'subjectOfferings' => $subjectOfferings,
            'totalUnits' => $totalUnits,
            'schoolYear' => $schoolYear,
        ]);
    }

    public function records()
    {
        $user = Auth::user();

        $enrollments = Enrollment::where('student_id', $user->id)
            ->whereHas('grades', fn ($q) => $q->where('status', 'submitted'))
            ->with(['section', 'schoolYear'])
            ->get();

        $terms = $enrollments->groupBy(function ($e) {
            return $e->schoolYear ? $e->schoolYear->name . ' — ' . $e->schoolYear->semester : 'Unknown';
        });

        $recordData = [];
        foreach ($terms as $termName => $termEnrollments) {
            $grades = Grade::where('student_id', $user->id)
                ->where('status', 'submitted')
                ->whereIn('teacher_assignment_id',
                    \App\Models\TeacherSubject::whereIn('section_id', $termEnrollments->pluck('section_id'))->pluck('id'))
                ->with(['teacherAssignment.subject', 'subject'])
                ->get();

            $recordData[] = [
                'term' => $termName,
                'grades' => $grades,
                'gwa' => $this->computeGwa($grades),
                'totalUnits' => $grades->sum(fn ($g) => $g->subject?->units ?? 0),
            ];
        }

        return view('student.records', [
            'records' => $recordData,
            'schoolYears' => SchoolYear::where('is_active', true)->latest()->get(),
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $enrollment = Enrollment::where('student_id', $user->id)
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with('section')
            ->first();

        $section = $enrollment?->section;

        $assignmentIds = \App\Models\TeacherSubject::where('section_id', optional($section)->id)
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->pluck('id');

        $grades = Grade::where('student_id', $user->id)
            ->whereIn('teacher_assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->with(['teacherAssignment.subject', 'subject'])
            ->get();

        $gwa = $this->computeGwa($grades);

        return view('student.profile', [
            'user' => $user,
            'section' => $section,
            'schoolYear' => $schoolYear,
            'gwa' => $gwa,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $sexes = \App\Http\Controllers\Auth\RegisterController::SEXES;

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'sex' => ['required', 'string', 'in:' . implode(',', $sexes)],
            'email' => [
                'required', 'email', 'max:255',
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:32'],
        ], [
            'sex.in' => 'Please select a valid sex.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}
