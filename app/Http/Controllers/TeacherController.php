<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\TeacherSubject;
use App\Models\User;
use App\Models\Setting;
use App\Support\GradingScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ClassOfferingStudent;

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
        $values = array_filter([$prelim, $midterm, $final], fn($v) => $v !== null);
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
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('status', 'active')
            ->with(['subject', 'section', 'schoolYear'])
            ->get();

        $assignmentIds = $assignedClasses->pluck('id');

        $totalStudents = ClassOfferingStudent::whereIn('teacher_subject_id', $assignmentIds)
            ->distinct('student_id')->count('student_id');

        // Add the count of irregular students explicitly assigned to any
        // of this teacher's class offerings, so the dashboard reflects
        // the total roster.
        if ($assignmentIds->isNotEmpty()) {
            $explicitCount = \App\Models\ClassOfferingStudent::whereIn('teacher_subject_id', $assignmentIds)
                ->distinct('student_id')
                ->count('student_id');
            $totalStudents += (int) $explicitCount;
        }

        $assignmentIds = $assignedClasses->pluck('id');
        $pendingGrades = Grade::whereIn('teacher_assignment_id', $assignmentIds)
            ->where('status', 'draft')
            ->count();

        // Per-class progress: total students enrolled vs. grade rows submitted for that assignment.
        $submittedByAssignment = Grade::whereIn('teacher_assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->selectRaw('teacher_assignment_id, COUNT(*) as submitted_count, AVG(final_grade) as avg_grade')
            ->groupBy('teacher_assignment_id')
            ->get()
            ->keyBy('teacher_assignment_id');

        $classRows = $assignedClasses->map(function ($class) use ($submittedByAssignment) {
            $total = ClassOfferingStudent::where('teacher_subject_id', $class->id)->count();
            $row = $submittedByAssignment->get($class->id);
            // `$row` is null when this assignment has zero submitted grades;
            // the GROUP BY query simply doesn't return a row for it. Treat
            // that as "nothing submitted" with no average instead of trying
            // to read a property on null (which was throwing an
            // "Attempt to read property avg_grade on null" error on every
            // teacher dashboard load).
            $submitted = (int) ($row?->submitted_count ?? 0);
            $avg = isset($row?->avg_grade) ? round((float) $row->avg_grade, 2) : null;
            $pct = $total > 0 ? (int) round(($submitted / $total) * 100) : 0;
            return [
                'id' => $class->id,
                'subject' => $class->subject,
                'section' => $class->section,
                'schoolYear' => $class->schoolYear,
                'total' => $total,
                'submitted' => $submitted,
                'pct' => $pct,
                'average' => $avg,
            ];
        });

        // Recent grades across all of this teacher's classes.
        $recentGrades = Grade::with(['studentUser', 'subject'])
            ->where('teacher_id', $user->id)
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        return view('teacher.dashboard', [
            'assignedClasses' => $assignedClasses,
            'classRows' => $classRows,
            'recentGrades' => $recentGrades,
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
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
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
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('status', 'active')
            ->with(['subject', 'section'])
            ->get();

        $subjects = $assignments
            ->groupBy('subject_id')
            ->map(fn($group) => $group->first()->subject)
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
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
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
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('status', 'active')
            ->with(['section', 'subject'])
            ->get();

        $sectionStudents = $assignments
            ->map(fn($a) => $a->section)
            ->filter()
            ->map(fn($s) => $s->students)
            ->flatten()
            ->unique('id');

        $assignmentIds = $assignments->pluck('id');
        $explicitStudents = User::whereIn('id', function ($q) use ($assignmentIds) {
            $q->select('student_id')->from('class_offering_student')->whereIn('teacher_subject_id', $assignmentIds);
        })
            ->where('role', 'student')
            ->get();

        $students = $sectionStudents
            ->merge($explicitStudents)
            ->unique('id')
            ->sortBy(fn($s) => $s->last_name_first ?: $s->name)
            ->values();

        // Filter option lists for the table: each value is a string the
        // client-side filter matches against the corresponding column. We
        // pull them from the same source data the table renders (the
        // students' `course` column and the assignments' subject codes)
        // so the dropdowns are guaranteed to match at least one row.
        $courseOptions = $students
            ->pluck('course')
            ->filter()
            ->map(fn($c) => \App\Models\Section::shortCourse((string) $c))
            ->unique()
            ->sort()
            ->values();

        $subjectOptions = $assignments
            ->pluck('subject.code')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('teacher.students', [
            'students' => $students,
            'assignments' => $assignments,
            'schoolYear' => $schoolYear,
            'courseOptions' => $courseOptions,
            'subjectOptions' => $subjectOptions,
        ]);
    }

    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:255', 'unique:users,student_id'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        User::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
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
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
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
            $class->total_students = ClassOfferingStudent::where('teacher_subject_id', $class->id)->count();
        }

        return view('teacher.grades', [
            'classes' => $classes,
            'schoolYear' => $schoolYear,
        ]);
    }

    /**
     * Show the grade entry form for a specific assignment.
     *
     * Students are pulled from two sources so an irregular student
     * assigned to this specific class offering (by an admin) appears
     * here even though they belong to a different section:
     *   1. Section-level enrollments (the legacy path — keeps pre-existing
     *      data working without a re-sync).
     *   2. Explicit class_offering_student rows for this assignment.
     *
     * EnrollmentSyncService::ensureDraftGrade() runs whenever a
     * ClassOfferingStudent row is created, so explicit students will
     * also have a real enrollment row in the offering's section — we
     * just need to look it up.
     */
    public function enterGrades(TeacherSubject $teacherSubject)
    {
        $user = Auth::user();

        // Spec §40: a teacher must never access another teacher's assignment.
        abort_unless($teacherSubject->teacher_id === $user->id, 403);

        $schoolYear = $this->currentSchoolYear();

        $teacherSubject->load(['subject', 'section', 'schoolYear']);

        $enrollments = Enrollment::whereIn('student_id', $this->rosterStudentIds($teacherSubject))
            ->where('section_id', $teacherSubject->section_id)
            ->where('status', 'enrolled')
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->with('student')
            ->get()
            ->sortBy(fn($e) => $e->student?->last_name_first ?: ($e->student?->name ?? ''))
            ->values();

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

        $allowed = $this->rosterStudentIds($teacherSubject)->all();
        abort_if(
            collect($payload)->contains(fn($r) => !in_array($r['student_id'], $allowed)),
            403,
            'Student is not enrolled in this subject.'
        );

        $isSubmit = $request->has('action') && $request->input('action') === 'submit';
        $submittedAt = $isSubmit ? now() : null;

        foreach ($payload as $row) {
            $prelim = $row['prelim'] ?? null;
            $midterm = $row['midterm'] ?? null;
            $final = $row['final'] ?? null;
            $finalGrade = $this->computeFinalGrade($prelim, $midterm, $final);
            $remarks = GradingScale::remark($finalGrade);

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
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
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
            $sub->total_students = ClassOfferingStudent::where('teacher_subject_id', $sub->id)->count();
        }

        return view('teacher.reports', [
            'submissions' => $submissions,
            'schoolYear' => $schoolYear,
        ]);
    }

    /**
     * Show a detailed per-class grade report for one of the logged-in
     * teacher's class offerings. Mirrors AdminReportController::show
     * but is scoped to the teacher's own assignments and has a
     * dedicated printable variant (see classReportPrint()).
     */
    public function classReport(TeacherSubject $teacherSubject)
    {
        $user = Auth::user();

        // Spec §40: a teacher must never access another teacher's assignment.
        abort_unless($teacherSubject->teacher_id === $user->id, 403);

        $data = $this->buildClassReportData($teacherSubject);

        return view('teacher.reports-show', $data);
    }

    /**
     * Print-only variant of the per-class report. Bypasses the app
     * shell so the browser's print preview shows just the report —
     * institutional header + grade matrix — with no sidebar/navbar.
     */
    public function classReportPrint(TeacherSubject $teacherSubject)
    {
        $user = Auth::user();
        abort_unless($teacherSubject->teacher_id === $user->id, 403);

        $data = $this->buildClassReportData($teacherSubject);
        $signaturePath = Setting::get('registrar_signature_path');
        $data['registrarSignatureUrl'] = $signaturePath
            ? route('registrar-signature')
            : null;

        return view('teacher.reports-print', $data);
    }

    /**
     * Build the data array shared by classReport() and classReportPrint().
     * Two student sources are merged so an irregular student explicitly
     * assigned to this specific class offering appears in the matrix
     * even though they belong to a different section.
     */
    protected function buildClassReportData(TeacherSubject $teacherSubject): array
    {
        $teacherSubject->load(['subject', 'section', 'teacher', 'schoolYear']);

        $enrollments = Enrollment::with([
            'student',
            'grade' => fn($q) => $q->where('teacher_assignment_id', $teacherSubject->id),
        ])
            ->whereIn('student_id', $this->rosterStudentIds($teacherSubject))
            ->where('section_id', $teacherSubject->section_id)
            ->where('status', 'enrolled')
            ->get();

        $gradeRows = $enrollments->map(function ($enrollment) use ($teacherSubject) {
            $grade = $enrollment->grade;
            $student = $enrollment->student;
            return [
                'student_id' => $enrollment->student_id,
                'student_name' => $student?->last_name_first ?: ($student?->name ?? '—'),
                'student_no' => $student?->student_id ?? '—',
                'course' => $student?->short_course_name,
                'year_level' => $student?->year_level,
                'block' => $student?->block,
                'prelim' => $grade?->prelim,
                'midterm' => $grade?->midterm,
                'final' => $grade?->final,
                'final_grade' => $grade?->final_grade,
                'remarks' => $grade?->remarks,
                'status' => $grade?->status,
                'submitted_at' => $grade?->submitted_at,
            ];
        });

        $submittedCount = $gradeRows->where('status', 'submitted')->count();
        $gradedCount = $gradeRows->filter(fn($r) => $r['final_grade'] !== null)->count();
        $classAverage = $gradeRows->filter(fn($r) => $r['final_grade'] !== null)
            ->avg(fn($r) => (float) $r['final_grade']);

        $distribution = [
            '90-100' => $gradeRows->filter(fn($r) => $r['final_grade'] !== null && $r['final_grade'] >= 90 && $r['final_grade'] <= 100)->count(),
            '85-89' => $gradeRows->filter(fn($r) => $r['final_grade'] !== null && $r['final_grade'] >= 85 && $r['final_grade'] < 90)->count(),
            '80-84' => $gradeRows->filter(fn($r) => $r['final_grade'] !== null && $r['final_grade'] >= 80 && $r['final_grade'] < 85)->count(),
            '75-79' => $gradeRows->filter(fn($r) => $r['final_grade'] !== null && $r['final_grade'] >= 75 && $r['final_grade'] < 80)->count(),
            'Below 75' => $gradeRows->filter(fn($r) => $r['final_grade'] !== null && $r['final_grade'] < 75)->count(),
        ];

        return [
            'assignment' => $teacherSubject,
            'gradeRows' => $gradeRows,
            'totalStudents' => $gradeRows->count(),
            'submittedCount' => $submittedCount,
            'gradedCount' => $gradedCount,
            'classAverage' => $classAverage !== null ? round((float) $classAverage, 2) : null,
            'distribution' => $distribution,
        ];
    }

    public function profile()
    {
        $user = Auth::user();
        $schoolYear = $this->currentSchoolYear();

        $assignments = $user->teacherAssignments()
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
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
        $sexes = \App\Http\Controllers\Auth\RegisterController::SEXES;

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'sex' => ['required', 'string', 'in:' . implode(',', $sexes)],
            'email' => [
                'required',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id),
                'ends_with:@ckcgingoog.edu.ph',
            ],
            'phone' => ['nullable', 'string', 'max:32'],
        ], [
            'sex.in' => 'Please select a valid sex.',
            'email.ends_with' => 'Teacher email must be a valid @ckcgingoog.edu.ph address.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    protected function rosterStudentIds(TeacherSubject $ts)
    {
        return ClassOfferingStudent::where('teacher_subject_id', $ts->id)
            ->pluck('student_id');
    }
}
