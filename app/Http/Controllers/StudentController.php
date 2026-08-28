<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Enrollment;
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

        $enrollments = Enrollment::where('student_id', $user->id)
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with(['section'])
            ->get();

        $section = $enrollments->first()->section ?? null;

        $subjectIds = Grade::whereIn('enrollment_id', $enrollments->pluck('id'))
            ->when($schoolYear, fn ($q) => $q->whereHas('enrollment', fn ($q) => $q->where('school_year_id', $schoolYear->id)))
            ->pluck('subject_id')
            ->unique();

        $grades = Grade::where('teacher_id', '!=', null)
            ->whereIn('subject_id', $subjectIds)
            ->whereIn('enrollment_id', $enrollments->pluck('id'))
            ->with('subject')
            ->get();

        $enrolledSubjects = Subject::whereIn('id', $subjectIds)->get();
        $totalUnits = $enrolledSubjects->sum('units');
        $gwa = $this->computeGwa($grades);

        return view('student.dashboard', [
            'user' => $user,
            'section' => $section,
            'enrolledSubjects' => $enrolledSubjects,
            'totalUnits' => $totalUnits,
            'grades' => $grades,
            'gwa' => $gwa,
            'schoolYear' => $schoolYear,
        ]);
    }

    public function grades()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $enrollments = Enrollment::where('student_id', $user->id)
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->get();

        $gradeRecords = Grade::whereIn('enrollment_id', $enrollments->pluck('id'))
            ->where('status', 'submitted')
            ->with('subject')
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

        $enrollments = Enrollment::where('student_id', $user->id)
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with('section.subjects')
            ->get();

        $subjectIds = $enrollments->flatMap(fn ($e) => $e->section->subjects->pluck('id'))->unique();

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

        // All completed semesters (enrollments with submitted grades)
        $enrollments = Enrollment::where('student_id', $user->id)
            ->whereHas('grades', fn ($q) => $q->where('status', 'submitted'))
            ->with(['section', 'schoolYear'])
            ->get();

        // Group by school year for term-based display
        $terms = $enrollments->groupBy(function ($e) {
            return $e->schoolYear ? $e->schoolYear->name . ' — ' . $e->schoolYear->semester : 'Unknown';
        });

        $recordData = [];
        foreach ($terms as $termName => $termEnrollments) {
            $subjectIds = $termEnrollments->flatMap(fn ($e) => Grade::where('enrollment_id', $e->id)->pluck('subject_id'))->unique();
            $grades = Grade::whereIn('enrollment_id', $termEnrollments->pluck('id'))
                ->where('status', 'submitted')
                ->with('subject')
                ->get();

            $recordData[] = [
                'term' => $termName,
                'grades' => $grades,
                'gwa' => $this->computeGwa($grades),
                'totalUnits' => $grades->sum('subject.units'),
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

        $subjectIds = Grade::whereIn('enrollment_id', Enrollment::where('student_id', $user->id)->pluck('id'))
            ->pluck('subject_id')
            ->unique();

        $grades = Grade::where('teacher_id', '!=', null)
            ->whereIn('subject_id', $subjectIds)
            ->whereHas('enrollment', fn ($q) => $q->where('student_id', $user->id))
            ->where('status', 'submitted')
            ->with('subject')
            ->get();

        $gwa = $this->computeGwa($grades);

        return view('student.profile', [
            'user' => $user,
            'section' => $section,
            'schoolYear' => $schoolYear,
            'gwa' => $gwa,
        ]);
    }
}
