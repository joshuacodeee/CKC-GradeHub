<?php

namespace App\Http\Controllers;

use App\Models\ClassOfferingStudent;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use App\Services\EnrollmentSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RegistrarController extends Controller
{
    private const SEMESTERS = ['First Semester', 'Second Semester', 'Summer'];

    public function dashboard()
    {
        $schoolYear = SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::where('is_active', true)->latest()->first();

        return view('registrar.dashboard', [
            'studentCount' => User::where('role', 'student')->count(),
            'irregularCount' => User::where('role', 'student')->where('is_irregular', true)->count(),
            'enrollmentCount' => ClassOfferingStudent::whereHas('student', fn ($query) => $query->where('role', 'student'))->count(),
            'schoolYear' => $schoolYear,
            'recentIrregularStudents' => User::where('role', 'student')
                ->where('is_irregular', true)
                ->alphabetical()
                ->limit(8)
                ->get(),
        ]);
    }

    public function students(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $standing = (string) $request->query('standing', '');
        abort_unless(in_array($standing, ['', 'regular', 'irregular'], true), 422);

        $students = User::query()
            ->where('role', 'student')
            ->when($standing !== '', fn ($query) => $query->where('is_irregular', $standing === 'irregular'))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('student_id', 'like', "%{$search}%")
                        ->orWhere('firstname', 'like', "%{$search}%")
                        ->orWhere('middlename', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('course', 'like', "%{$search}%");
                });
            })
            ->alphabetical()
            ->paginate(25)
            ->withQueryString();

        return view('registrar.students', compact('students', 'search', 'standing'));
    }

    public function show(User $student, Request $request)
    {
        $this->ensureStudent($student);
        $filters = $this->periodFilters($request);

        $grades = $this->academicGrades($student, $filters);
        $memberships = $this->classMemberships($student, $filters);
        $gradeByAssignment = Grade::where('student_id', $student->id)
            ->whereIn('teacher_assignment_id', $memberships->pluck('teacher_subject_id'))
            ->get()
            ->keyBy('teacher_assignment_id');

        foreach ($memberships as $membership) {
            $membership->gradeRecord = $gradeByAssignment->get($membership->teacher_subject_id);
        }

        $offerings = TeacherSubject::query()
            ->where('status', 'active')
            ->whereNotNull('school_year_id')
            ->with(['subject', 'section', 'teacher', 'schoolYear'])
            ->orderBy('semester')
            ->get();

        $updatedBy = Setting::get('registrar_signature_updated_by');

        return view('registrar.student', [
            'student' => $student,
            'grades' => $grades,
            'memberships' => $memberships,
            'offerings' => $offerings,
            'subjects' => Subject::where('is_active', true)->orderBy('code')->get(),
            'schoolYears' => SchoolYear::orderByDesc('created_at')->get(),
            'filters' => $filters,
            'signatoryName' => Setting::get('registrar_signatory_name', 'College Registrar'),
            'signatoryPosition' => Setting::get('registrar_signatory_position', 'College Registrar'),
            'updatedBy' => $updatedBy ? User::find($updatedBy) : null,
            'signatureUpdatedAt' => Setting::get('registrar_signature_updated_at'),
        ]);
    }

    public function toggleIrregular(User $student)
    {
        $this->ensureStudent($student);

        $sync = DB::transaction(function () use ($student) {
            $lockedStudent = User::whereKey($student->id)->lockForUpdate()->firstOrFail();
            $lockedStudent->is_irregular = ! $lockedStudent->is_irregular;
            $lockedStudent->save();

            return app(EnrollmentSyncService::class)->syncForStudent($lockedStudent);
        });

        $student->refresh();

        return redirect()
            ->route('registrar.students.show', $student)
            ->with('success', $student->is_irregular
                ? 'Student marked as irregular. Automatic subject assignment is paused.'
                : "Student marked as regular. Synced {$sync['added_offerings']} class offerings.");
    }

    public function assignSubject(Request $request, User $student)
    {
        $this->ensureStudent($student);

        if (! $student->is_irregular) {
            return back()->with('error', 'Mark the student as irregular before assigning individual subjects.');
        }

        $data = $request->validate([
            'teacher_subject_id' => ['required', 'integer', 'exists:teacher_subjects,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'school_year_id' => ['required', 'integer', 'exists:school_years,id'],
            'semester' => ['required', 'in:'.implode(',', self::SEMESTERS)],
        ]);

        $offering = TeacherSubject::query()
            ->whereKey($data['teacher_subject_id'])
            ->where('status', 'active')
            ->where('subject_id', $data['subject_id'])
            ->where('section_id', $data['section_id'])
            ->where('school_year_id', $data['school_year_id'])
            ->where('semester', $data['semester'])
            ->first();

        if (! $offering) {
            return back()
                ->withErrors(['teacher_subject_id' => 'Choose an active teacher offering that matches the selected subject and academic period.'])
                ->withInput();
        }

        $assigned = app(EnrollmentSyncService::class)
            ->assignToOffering($student, $offering, $request->user());

        if (! $assigned) {
            return back()->with('error', 'This student is already enrolled in that subject for the selected school year and semester.');
        }

        return redirect()
            ->route('registrar.students.show', $student)
            ->with('success', 'Subject assigned to the student and added to the teacher roster.');
    }

    public function printReport(User $student, Request $request)
    {
        $this->ensureStudent($student);
        $filters = $this->periodFilters($request);
        $grades = $this->academicGrades($student, $filters)
            ->where('status', 'submitted')
            ->values();

        return view('registrar.report', [
            'student' => $student,
            'grades' => $grades,
            'filters' => $filters,
            'schoolName' => Setting::get('school_name', 'Christ the King College'),
            'signatureUrl' => Setting::get('registrar_signature_path')
                ? route('registrar-signature')
                : null,
            'signatoryName' => Setting::get('registrar_signatory_name', 'College Registrar'),
            'signatoryPosition' => Setting::get('registrar_signatory_position', 'College Registrar'),
        ]);
    }

    private function ensureStudent(User $student): void
    {
        abort_unless($student->role === 'student', 404);
    }

    /**
     * @return array{school_year_id:?string, semester:?string}
     */
    private function periodFilters(Request $request): array
    {
        $filters = $request->validate([
            'school_year_id' => ['nullable', 'integer', 'exists:school_years,id'],
            'semester' => ['nullable', 'in:'.implode(',', self::SEMESTERS)],
        ]);

        return [
            'school_year_id' => $filters['school_year_id'] ?? null,
            'semester' => $filters['semester'] ?? null,
        ];
    }

    /**
     * @param  array{school_year_id:?string, semester:?string}  $filters
     * @return Collection<int, Grade>
     */
    private function academicGrades(User $student, array $filters): Collection
    {
        return Grade::query()
            ->where('student_id', $student->id)
            ->with([
                'subject',
                'teacher',
                'enrollment.section',
                'enrollment.schoolYear',
                'teacherAssignment.subject',
                'teacherAssignment.teacher',
                'teacherAssignment.section',
                'teacherAssignment.schoolYear',
            ])
            ->get()
            ->filter(function (Grade $grade) use ($filters) {
                $assignment = $grade->teacherAssignment;
                $schoolYearId = $assignment?->school_year_id ?? $grade->enrollment?->school_year_id;
                $semester = $assignment?->semester
                    ?? $grade->enrollment?->schoolYear?->semester;

                return (! $filters['school_year_id'] || (string) $schoolYearId === (string) $filters['school_year_id'])
                    && (! $filters['semester'] || $semester === $filters['semester']);
            })
            ->sortByDesc(fn (Grade $grade) => $grade->teacherAssignment?->schoolYear?->created_at
                ?? $grade->enrollment?->schoolYear?->created_at
                ?? $grade->updated_at)
            ->values();
    }

    /**
     * @param  array{school_year_id:?string, semester:?string}  $filters
     * @return Collection<int, ClassOfferingStudent>
     */
    private function classMemberships(User $student, array $filters): Collection
    {
        return ClassOfferingStudent::query()
            ->where('student_id', $student->id)
            ->with([
                'teacherSubject.subject',
                'teacherSubject.teacher',
                'teacherSubject.section',
                'teacherSubject.schoolYear',
            ])
            ->get()
            ->filter(function (ClassOfferingStudent $membership) use ($filters) {
                $offering = $membership->teacherSubject;
                $schoolYearId = $membership->school_year_id ?? $offering?->school_year_id;

                return $offering
                    && (! $filters['school_year_id'] || (string) $schoolYearId === (string) $filters['school_year_id'])
                    && (! $filters['semester'] || $offering->semester === $filters['semester']);
            })
            ->values();
    }
}
