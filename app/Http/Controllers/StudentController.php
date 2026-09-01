<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Subject;
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
        // students get linked as soon as a section is created.
        $this->ensureEnrollment($user, $schoolYear);

        $enrollments = Enrollment::where('student_id', $user->id)
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('status', 'enrolled')
            ->with('section')
            ->get();

        $section = $enrollments->first()?->section;

        // All subjects the student should see this term: those that have
        // a teacher assignment in the student's section (regardless of
        // whether a grade has been encoded yet). If no school-year-scoped
        // assignments exist for the section, fall back to any active
        // assignment for that section — useful when the section was created
        // without a school year.
        $teacherSubjectQuery = \App\Models\TeacherSubject::where('section_id', optional($section)->id)
            ->where('status', 'active');
        if ($schoolYear) {
            $teacherSubjectQuery->where(function ($q) use ($schoolYear) {
                $q->where('school_year_id', $schoolYear->id)
                    ->orWhereNull('school_year_id');
            });
        }

        $subjectIds = $teacherSubjectQuery->pluck('subject_id')->unique();

        $enrolledSubjects = Subject::whereIn('id', $subjectIds)->get();
        $totalUnits = $enrolledSubjects->sum('units');

        $grades = Grade::where('student_id', $user->id)
            ->whereIn('teacher_assignment_id', \App\Models\TeacherSubject::whereIn('subject_id', $subjectIds)->pluck('id'))
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
     */
    protected function ensureEnrollment($user, $schoolYear): void
    {
        $alreadyEnrolled = Enrollment::where('student_id', $user->id)
            ->where('status', 'enrolled')
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->exists();

        if ($alreadyEnrolled) {
            return;
        }

        $studentBlock = $user->block ?: null;

        $section = \App\Models\Section::query()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('course', $user->course)
            ->where('year_level', $user->year_level)
            ->when($studentBlock === null, function ($q) {
                $q->where(function ($q) {
                    $q->whereNull('block')->orWhere('block', '');
                });
            })
            ->when($studentBlock !== null, fn ($q) => $q->where('block', $studentBlock))
            ->where('is_active', true)
            ->first();

        if (! $section) {
            return;
        }

        Enrollment::create([
            'student_id' => $user->id,
            'section_id' => $section->id,
            'school_year_id' => $schoolYear?->id,
            'status' => 'enrolled',
        ]);
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
            ->with(['teacherAssignment.subject', 'subject'])
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

        $enrollments = Enrollment::where('student_id', $user->id)
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->get();

        $sectionIds = $enrollments->pluck('section_id');

        $subjectIds = \App\Models\TeacherSubject::whereIn('section_id', $sectionIds)
            ->when($schoolYear, function ($q) use ($schoolYear) {
                $q->where(function ($q) use ($schoolYear) {
                    $q->where('school_year_id', $schoolYear->id)
                        ->orWhereNull('school_year_id');
                });
            })
            ->where('status', 'active')
            ->pluck('subject_id')
            ->unique();

        $subjects = Subject::whereIn('id', $subjectIds)->get();
        $totalUnits = $subjects->sum('units');

        return view('student.subjects', [
            'subjects' => $subjects,
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

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:32'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}
