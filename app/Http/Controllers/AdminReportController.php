<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Section;
use App\Models\User;

class AdminReportController extends Controller
{
    public function index()
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
            if ($schoolYear) {
                $teacherSubjects = \App\Models\TeacherSubject::where('subject_id', $subject->id)
                    ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
                    ->with(['teacher', 'section'])
                    ->get();
            } else {
                $teacherSubjects = \App\Models\TeacherSubject::where('subject_id', $subject->id)
                    ->with(['teacher', 'section'])
                    ->get();
            }

            foreach ($teacherSubjects as $ts) {
                $enrollmentIds = \App\Models\Enrollment::where('section_id', $ts->section_id)
                    ->pluck('id');

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
                    'subject' => $subject->name,
                    'code' => $subject->code,
                    'section' => $ts->section?->name ?? '—',
                    'teacher' => $ts->teacher?->name ?? '—',
                    'students' => $totalStudents,
                    'avg' => $avgGrade ? round($avgGrade, 2) : '—',
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
        ]);
    }
}
