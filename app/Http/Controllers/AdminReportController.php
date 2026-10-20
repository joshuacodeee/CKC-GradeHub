<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Setting;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $schoolYear = SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::where('is_active', true)->latest()->first()
            ?? SchoolYear::latest()->first();

        // Build submission status per subject+section
        $subjects = Subject::where('is_active', true)->latest()->get();
        $sections = Section::withCount(['enrollments' => function ($q) {
            $q->where('status', 'enrolled');
        }])->get();
        $teachers = User::where('role', 'teacher')->get();

        $reportRows = [];
        foreach ($subjects as $subject) {
            $teacherSubjects = TeacherSubject::where('subject_id', $subject->id)
                ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
                ->with(['teacher', 'section'])
                ->get();

            foreach ($teacherSubjects as $ts) {
                // Guard against teacher_subjects rows that have no section
                // (data drift / legacy imports) — without a section there is
                // no roster to report against. Show the row with zero counts
                // rather than throwing on null section_id.
                $enrollmentIds = $ts->section_id
                    ? Enrollment::where('section_id', $ts->section_id)->pluck('id')
                    : collect();

                $totalStudents = $enrollmentIds->count();
                $submitted = Grade::whereIn('enrollment_id', $enrollmentIds)
                    ->where('subject_id', $subject->id)
                    ->where('status', 'submitted')
                    ->count();

                $avgGrade = Grade::whereIn('enrollment_id', $enrollmentIds)
                    ->where('subject_id', $subject->id)
                    ->avg('final_grade');

                $status = $totalStudents > 0 && $submitted === $totalStudents
                    ? 'Submitted'
                    : ($submitted > 0 ? 'Partial' : 'Pending');

                $reportRows[] = [
                    'assignment_id' => $ts->id,
                    'subject' => $subject->name,
                    'code' => $subject->code,
                    'section' => $ts->section?->name ?? '—',
                    'teacher' => $ts->teacher?->name ?? '—',
                    'students' => $totalStudents,
                    'avg' => $avgGrade ? round($avgGrade, 2) : '—',
                    'avg_equivalent' => $avgGrade
                        ? \App\Support\GradingScale::equivalent((float) $avgGrade)
                        : null,
                    'submitted' => $submitted,
                    'status' => $status,
                ];
            }
        }

        $submittedCount = collect($reportRows)->where('status', 'Submitted')->count();
        $pendingCount = collect($reportRows)->where('status', 'Pending')->count();
        $schoolAverage = collect($reportRows)->filter(fn ($r) => is_numeric($r['avg']))->avg('avg');

        return view('admin.reports', [
            'reportRows' => $reportRows,
            'submittedCount' => $submittedCount,
            'pendingCount' => $pendingCount,
            'schoolAverage' => $schoolAverage ? round($schoolAverage, 1) : '—',
            'schoolYear' => $schoolYear,
            'schoolYears' => SchoolYear::where('is_active', true)->latest()->get(),
            'role' => $request->user()->role,
            'signatureUrl' => Setting::get('registrar_signature_path')
                ? route('registrar-signature')
                : null,
            'signatoryName' => Setting::get('registrar_signatory_name', 'College Registrar'),
            'signatoryPosition' => Setting::get('registrar_signatory_position', 'College Registrar'),
        ]);
    }

    public function show(TeacherSubject $assignment, Request $request)
    {
        $assignment->load(['subject', 'section', 'teacher', 'schoolYear']);

        // Source 1: students in the section (legacy path).
        $enrollments = Enrollment::with(['student', 'grade' => function ($q) use ($assignment) {
                $q->where('teacher_assignment_id', $assignment->id);
            }])
            ->where('section_id', $assignment->section_id)
            ->where('status', 'enrolled')
            ->get()
            ->keyBy('student_id');

        // Source 2: explicit per-class-offering memberships (irregular
        // students assigned to this specific offering by a registrar).
        $explicitStudentIds = \App\Models\ClassOfferingStudent::where('teacher_subject_id', $assignment->id)
            ->pluck('student_id');

        $explicitStudents = User::whereIn('id', $explicitStudentIds)
            ->where('role', 'student')
            ->whereNotIn('id', $enrollments->keys())
            ->with(['grades' => function ($q) use ($assignment) {
                $q->where('teacher_assignment_id', $assignment->id);
            }])
            ->get();

        foreach ($explicitStudents as $student) {
            $grade = $student->grades->first();
            $enrollments->put($student->id, (object) [
                'student' => $student,
                'student_id' => $student->id,
                'grade' => $grade,
            ]);
        }

        $enrollments = $enrollments->values()->sortBy(fn ($e) => $e->student?->last_name_first ?: ($e->student?->name ?? ''))->values();

        $gradeRows = $enrollments->map(function (Enrollment $enrollment) use ($assignment) {
            $grade = $enrollment->grade;
            return [
                'student_id'   => $enrollment->student_id,
                'student_name' => $enrollment->student?->name ?? '—',
                'student_no'   => $enrollment->student?->student_id ?? '—',
                'course'       => $enrollment->student?->course,
                'year_level'   => $enrollment->student?->year_level,
                'block'        => $enrollment->student?->block,
                'prelim'       => $grade?->prelim,
                'midterm'      => $grade?->midterm,
                'final'        => $grade?->final,
                'final_grade'  => $grade?->final_grade,
                'remarks'      => $grade?->remarks,
                'status'       => $grade?->status,
                'submitted_at' => $grade?->submitted_at,
            ];
        });

        $submittedCount = $gradeRows->where('status', 'submitted')->count();
        $gradedCount    = $gradeRows->filter(fn ($r) => $r['final_grade'] !== null)->count();
        $classAverage   = $gradeRows->filter(fn ($r) => $r['final_grade'] !== null)
            ->avg(fn ($r) => (float) $r['final_grade']);

        $distribution = [
            '90-100'    => $gradeRows->filter(fn ($r) => $r['final_grade'] !== null && $r['final_grade'] >= 90 && $r['final_grade'] <= 100)->count(),
            '85-89'     => $gradeRows->filter(fn ($r) => $r['final_grade'] !== null && $r['final_grade'] >= 85 && $r['final_grade'] < 90)->count(),
            '80-84'     => $gradeRows->filter(fn ($r) => $r['final_grade'] !== null && $r['final_grade'] >= 80 && $r['final_grade'] < 85)->count(),
            '75-79'     => $gradeRows->filter(fn ($r) => $r['final_grade'] !== null && $r['final_grade'] >= 75 && $r['final_grade'] < 80)->count(),
            'Below 75'  => $gradeRows->filter(fn ($r) => $r['final_grade'] !== null && $r['final_grade'] < 75)->count(),
        ];

        return view('admin.reports-show', [
            'assignment'      => $assignment,
            'gradeRows'       => $gradeRows,
            'totalStudents'   => $gradeRows->count(),
            'submittedCount'  => $submittedCount,
            'gradedCount'     => $gradedCount,
            'classAverage'    => $classAverage !== null ? round((float) $classAverage, 2) : null,
            'distribution'    => $distribution,
            'role'           => $request->user()->role,
            'signatureUrl'   => Setting::get('registrar_signature_path')
                ? route('registrar-signature')
                : null,
            'signatoryName' => Setting::get('registrar_signatory_name', 'College Registrar'),
            'signatoryPosition' => Setting::get('registrar_signatory_position', 'College Registrar'),
        ]);
    }
}
