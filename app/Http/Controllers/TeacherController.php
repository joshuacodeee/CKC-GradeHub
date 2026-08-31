<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
     * Spec §30: Final Grade = (Prelim + Midterm + Final) / 3.
     * Returns null if all three are null so views can show a dash.
     */
    protected function computeFinalGrade(?float $prelim, ?float $midterm, ?float $final): ?float
    {
        if ($prelim === null && $midterm === null && $final === null) {
            return null;
        }
        $values = array_filter([$prelim, $midterm, $final], fn ($v) => $v !== null);
        if (empty($values)) {
            return null;
        }
        return round(array_sum($values) / count($values), 2);
    }

    public function dashboard()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $assignedClasses = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('status', 'active')
            ->with(['subject', 'section', 'schoolYear'])
            ->get();

        $totalStudents = $assignedClasses->sum(
            fn ($c) => $c->section?->studentCount() ?? 0
        );

        $assignmentIds = $assignedClasses->pluck('id');
        $pendingGrades = Grade::whereIn('teacher_assignment_id', $assignmentIds)
            ->where('status', 'draft')
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
            ->where('status', 'active')
            ->with(['subject', 'section', 'schoolYear'])
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

        $assignments = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('status', 'active')
            ->with(['subject', 'section'])
            ->get();

        $subjects = $assignments
            ->groupBy('subject_id')
            ->map(fn ($group) => $group->first()->subject)
            ->values();

        $subjectDetails = $assignments->groupBy('subject_id');

        return view('teacher.subjects', [
            'subjects' => $subjects,
            'subjectDetails' => $subjectDetails,
            'schoolYear' => $schoolYear,
        ]);
    }

    /**
     * Spec §35 / §42: "My Sections" — sections assigned to the logged-in
     * teacher in the current school year, grouped by subject.
     */
    public function sections()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $assignments = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('status', 'active')
            ->with(['subject', 'section', 'schoolYear'])
            ->get()
            ->groupBy('subject_id');

        return view('teacher.sections', [
            'groupedAssignments' => $assignments,
            'schoolYear' => $schoolYear,
        ]);
    }

    public function students()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $assignments = $user->teacherAssignments()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('status', 'active')
            ->with(['section', 'subject'])
            ->get();

        $students = $assignments
            ->map(fn ($a) => $a->section)
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

    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:255', 'unique:users,student_id'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'student_id' => $validated['student_id'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'is_active' => true,
        ]);

        return redirect()->route('teacher.students')->with('success', 'Student account created successfully.');
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
            ->where('status', 'active')
            ->with(['subject', 'section'])
            ->get();

        foreach ($classes as $class) {
            $enrollmentIds = Enrollment::where('section_id', $class->section_id)
                ->where('status', 'enrolled')
                ->pluck('id');

            $class->total_students = $enrollmentIds->count();
            $class->grades_submitted = Grade::where('teacher_assignment_id', $class->id)
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
     * Show the grade entry form for a specific assignment.
     */
    public function enterGrades(Request $request, TeacherSubject $teacherSubject)
    {
        $user = Auth::user();

        // Spec §40: a teacher must never access another teacher's assignment.
        abort_unless($teacherSubject->teacher_id === $user->id, 403);

        $schoolYear = $this->currentSchoolYear();

        $teacherSubject->load(['subject', 'section', 'schoolYear']);

        $enrollments = Enrollment::where('section_id', $teacherSubject->section_id)
            ->where('status', 'enrolled')
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->with('student')
            ->get();

        // Existing grades keyed by student_id (user id).
        $existingGrades = Grade::where('teacher_assignment_id', $teacherSubject->id)
            ->whereIn('student_id', $enrollments->pluck('student_id'))
            ->get()
            ->keyBy('student_id');

        return view('teacher.grades-enter', [
            'teacherSubject' => $teacherSubject,
            'enrollments' => $enrollments,
            'existingGrades' => $existingGrades,
            'schoolYear' => $schoolYear,
        ]);
    }

    /**
     * Save grades as draft, or submit them if the submit action was used.
     */
    public function saveGrades(Request $request, TeacherSubject $teacherSubject)
    {
        $user = Auth::user();
        abort_unless($teacherSubject->teacher_id === $user->id, 403);

        $payload = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.enrollment_id' => ['required', 'exists:enrollments,id'],
            'grades.*.student_id' => ['required', 'exists:users,id'],
            'grades.*.prelim' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.midterm' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.final' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ])['grades'];

        $isSubmit = $request->has('action') && $request->input('action') === 'submit';
        $submittedAt = $isSubmit ? now() : null;

        foreach ($payload as $row) {
            $prelim = $row['prelim'] ?? null;
            $midterm = $row['midterm'] ?? null;
            $final = $row['final'] ?? null;
            $finalGrade = $this->computeFinalGrade($prelim, $midterm, $final);
            $remarks = $finalGrade !== null
                ? ($finalGrade >= 75 ? 'Passed' : 'Failed')
                : null;

            Grade::updateOrCreate(
                [
                    'teacher_assignment_id' => $teacherSubject->id,
                    'student_id' => $row['student_id'],
                ],
                [
                    'enrollment_id' => $row['enrollment_id'],
                    'subject_id' => $teacherSubject->subject_id,
                    'teacher_id' => $user->id,
                    'prelim' => $prelim,
                    'midterm' => $midterm,
                    'final' => $final,
                    'final_grade' => $finalGrade,
                    'remarks' => $remarks,
                    'status' => $isSubmit ? 'submitted' : 'draft',
                    'submitted_at' => $submittedAt,
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
            ->where('status', 'active')
            ->with(['subject', 'section'])
            ->get();

        foreach ($submissions as $sub) {
            $enrollmentIds = Enrollment::where('section_id', $sub->section_id)
                ->where('status', 'enrolled')
                ->pluck('id');

            $grades = Grade::where('teacher_assignment_id', $sub->id)->get();

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
            ->where('status', 'active')
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

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
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
