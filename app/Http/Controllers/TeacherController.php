<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Enrollment;
use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
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
     * Grade-weighting configuration — kept in sync with
     * computeFinalGrade() in public/js/app.js.
     */
    protected function gradeWeights(): array
    {
        return ['prelim' => 0.30, 'midterm' => 0.30, 'final' => 0.40];
    }

    protected function computeFinalGrade(?float $prelim, ?float $midterm, ?float $final): ?float
    {
        if ($prelim === null && $midterm === null && $final === null) {
            return null;
        }
        $w = $this->gradeWeights();
        return round(
            ($prelim * $w['prelim'] ?? 0) +
            ($midterm * $w['midterm'] ?? 0) +
            ($final * $w['final'] ?? 0),
            2
        );
    }

    public function dashboard()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $assignedClasses = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with(['subject', 'section'])
            ->get();

        $totalStudents = $assignedClasses->sum(fn ($c) => $c->section?->studentCount() ?? 0);

        $pendingGrades = Grade::whereIn('subject_id', $assignedClasses->pluck('subject_id'))
            ->where('status', 'draft')
            ->when($schoolYear, fn ($q) => $q->whereHas('enrollment', fn ($q) => $q->where('school_year_id', $schoolYear->id)))
            ->count();

        return view('teacher.dashboard', [
            'assignedClasses' => $assignedClasses,
            'totalStudents' => $totalStudents,
            'pendingGrades' => $pendingGrades,
            'schoolYear' => $schoolYear,
        ]);
    }

    public function classes()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $classes = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with(['subject', 'section'])
            ->get();

        return view('teacher.classes', [
            'classes' => $classes,
            'schoolYear' => $schoolYear,
        ]);
    }

    public function subjects()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $subjects = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with('subject')
            ->get()
            ->groupBy('subject_id')
            ->map(fn ($group) => $group->first()->subject)
            ->values();

        $subjectDetails = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with('subject', 'section')
            ->get()
            ->groupBy('subject_id');

        return view('teacher.subjects', [
            'subjects' => $subjects,
            'subjectDetails' => $subjectDetails,
            'schoolYear' => $schoolYear,
        ]);
    }

    public function students()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $assignments = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with(['section', 'subject'])
            ->get();

        $students = $assignments->map(fn ($a) => $a->section)
            ->filter()
            ->map(fn ($s) => $s->students)
            ->flatten()
            ->unique('id')
            ->values();

        return view('teacher.students', [
            'students' => $students,
            'assignments' => $assignments,
            'schoolYear' => $schoolYear,
        ]);
    }

    /**
     * Show the grade-entry index — list of classes the teacher can enter grades for.
     */
    public function grades()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $classes = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with(['subject', 'section'])
            ->get();

        // Compute how many students still have no submitted grades per class
        foreach ($classes as $class) {
            $enrollmentIds = Enrollment::where('section_id', $class->section_id)
                ->pluck('id');

            $class->total_students = $enrollmentIds->count();
            $class->grades_submitted = Grade::whereIn('enrollment_id', $enrollmentIds)
                ->where('subject_id', $class->subject_id)
                ->where('status', 'submitted')
                ->count();
            $class->pending_submissions = $class->total_students - $class->grades_submitted;
        }

        return view('teacher.grades', [
            'classes' => $classes,
            'schoolYear' => $schoolYear,
        ]);
    }

    /**
     * Show the grade entry form for a specific class + subject.
     */
    public function enterGrades(Request $request, TeacherSubject $teacherSubject)
    {
        $user = Auth::user();

        // Ensure the teacher actually owns this assignment
        abort_unless($teacherSubject->teacher_id === $user->id, 403);

        $schoolYear = $this->currentSchoolYear();

        $teacherSubject->load(['subject', 'section', 'schoolYear']);

        $enrollments = Enrollment::where('section_id', $teacherSubject->section_id)
            ->where('status', 'enrolled')
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with('student')
            ->get();

        // Existing grades keyed by student_id
        $existingGrades = Grade::where('teacher_id', $user->id)
            ->where('subject_id', $teacherSubject->subject_id)
            ->whereIn('enrollment_id', $enrollments->pluck('id'))
            ->get()
            ->keyBy(fn ($g) => $g->enrollment->student_id);

        return view('teacher.grades-enter', [
            'teacherSubject' => $teacherSubject,
            'enrollments' => $enrollments,
            'existingGrades' => $existingGrades,
            'schoolYear' => $schoolYear,
        ]);
    }

    /**
     * Save grades as draft.
     */
    public function saveGrades(Request $request, TeacherSubject $teacherSubject)
    {
        $user = Auth::user();
        abort_unless($teacherSubject->teacher_id === $user->id, 403);

        $grades = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.enrollment_id' => ['required', 'exists:enrollments,id'],
            'grades.*.student_id' => ['required', 'exists:users,id'],
            'grades.*.prelim' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.midterm' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.final' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ])['grades'];

        $isSubmit = $request->has('action') && $request->input('action') === 'submit';

        foreach ($grades as $gradeData) {
            $prelim = $gradeData['prelim'] ?? null;
            $midterm = $gradeData['midterm'] ?? null;
            $final = $gradeData['final'] ?? null;

            $finalGrade = $this->computeFinalGrade($prelim, $midterm, $final);
            $remarks = $finalGrade !== null
                ? ($finalGrade >= 75 ? 'Passed' : 'Failed')
                : null;

            Grade::updateOrCreate(
                [
                    'enrollment_id' => $gradeData['enrollment_id'],
                    'subject_id' => $teacherSubject->subject_id,
                    'teacher_id' => $user->id,
                ],
                [
                    'prelim' => $prelim,
                    'midterm' => $midterm,
                    'final' => $final,
                    'final_grade' => $finalGrade,
                    'remarks' => $remarks,
                    'status' => $isSubmit ? 'submitted' : 'draft',
                ]
            );
        }

        return redirect()->route('teacher.grades')
            ->with('success', $isSubmit
                ? 'Grades submitted successfully.'
                : 'Grades saved as draft.');
    }

    public function reports()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $submissions = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with(['subject', 'section'])
            ->get();

        foreach ($submissions as $sub) {
            $enrollmentIds = Enrollment::where('section_id', $sub->section_id)->pluck('id');
            $grades = Grade::where('teacher_id', $user->id)
                ->where('subject_id', $sub->subject_id)
                ->whereIn('enrollment_id', $enrollmentIds)
                ->get();

            $sub->total_students = $enrollmentIds->count();
            $sub->submitted_count = $grades->where('status', 'submitted')->count();
            $sub->avg_grade = $grades->avg('final_grade');
            $sub->status = $sub->total_students > 0 && $sub->submitted_count === $sub->total_students
                ? 'Submitted'
                : ($sub->submitted_count > 0 ? 'Partial' : 'Pending');
        }

        return view('teacher.reports', [
            'submissions' => $submissions,
            'schoolYear' => $schoolYear,
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $assignments = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with(['subject', 'section'])
            ->get();

        $subjects = $assignments->pluck('subject.name')->unique()->values();

        return view('teacher.profile', [
            'user' => $user,
            'assignments' => $assignments,
            'subjects' => $subjects,
            'schoolYear' => $schoolYear,
        ]);
    }
}
