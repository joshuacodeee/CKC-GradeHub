<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $schoolYear = SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::where('is_active', true)->latest()->first()
            ?? SchoolYear::latest()->first();

        $gradeQuery = Grade::query()
            ->whereNotNull('final_grade')
            ->when($schoolYear, function ($q) use ($schoolYear) {
                $q->whereHas('teacherAssignment', fn ($sub) => $sub->where('school_year_id', $schoolYear->id));
            });

        // Grade distribution buckets based on final_grade.
        $distribution = [
            '90-100' => (clone $gradeQuery)->whereBetween('final_grade', [90, 100])->count(),
            '85-89'  => (clone $gradeQuery)->whereBetween('final_grade', [85, 89.99])->count(),
            '80-84'  => (clone $gradeQuery)->whereBetween('final_grade', [80, 84.99])->count(),
            '75-79'  => (clone $gradeQuery)->whereBetween('final_grade', [75, 79.99])->count(),
            'Below 75' => (clone $gradeQuery)->where('final_grade', '<', 75)->count(),
        ];
        $totalGraded = array_sum($distribution);
        $distributionPct = [];
        foreach ($distribution as $band => $count) {
            $distributionPct[$band] = $totalGraded > 0
                ? round(($count / $totalGraded) * 100)
                : 0;
        }

        $recentGradeSubmissions = Grade::query()
            ->with(['studentUser', 'subject', 'teacher'])
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->limit(6)
            ->get();

        // Dynamic notifications for the navbar.
        $gradeNotifs = Grade::query()
            ->with(['teacher', 'subject', 'studentUser'])
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->limit(4)
            ->get()
            ->map(function (Grade $grade) {
                $teacher = $grade->teacher?->name ?? 'A teacher';
                $section = $grade->teacherAssignment?->section?->name ?? '';
                $subjectCode = $grade->subject?->code ?? '';
                $label = trim("{$subjectCode} {$section}");
                return [
                    'type'    => 'grade',
                    'icon'    => 'file-check-2',
                    'tone'    => 'info',
                    'title'   => "{$teacher} submitted grades" . ($label ? " for {$label}" : ''),
                    'time'    => $grade->submitted_at,
                ];
            });

        $studentNotifs = User::query()
            ->where('role', 'student')
            ->latest('created_at')
            ->limit(3)
            ->get()
            ->map(function (User $student) {
                return [
                    'type'  => 'student',
                    'icon'  => 'user-plus',
                    'tone'  => 'success',
                    'title' => "New student enrolled: {$student->name}",
                    'time'  => $student->created_at,
                ];
            });

        $notifications = $gradeNotifs
            ->merge($studentNotifs)
            ->sortByDesc('time')
            ->take(5)
            ->values()
            ->map(function (array $notif) {
                $notif['time_human'] = $notif['time']
                    ? Carbon::parse($notif['time'])->diffForHumans(null, true, true)
                    : 'just now';
                return $notif;
            });

        return view('admin.dashboard', [
            'studentCount'     => User::query()->where('role', 'student')->count(),
            'teacherCount'     => User::query()->where('role', 'teacher')->count(),
            'subjectCount'     => Subject::query()->count(),
            'sectionCount'     => Section::query()->where('is_active', true)->count(),
            'schoolYearCount'  => SchoolYear::query()->count(),
            'gradeRecordCount' => Grade::query()->count(),
            'assignmentCount'  => TeacherSubject::query()
                ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
                ->count(),
            'recentStudents'        => User::query()->where('role', 'student')->latest()->limit(5)->get(),
            'recentTeachers'        => User::query()->where('role', 'teacher')->latest()->limit(5)->get(),
            'schoolYear'            => $schoolYear,
            'gradeDistribution'     => $distribution,
            'gradeDistributionPct'  => $distributionPct,
            'totalGraded'           => $totalGraded,
            'recentGradeSubmissions' => $recentGradeSubmissions,
            'notifications'         => $notifications,
        ]);
    }
}
