<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $schoolYear = $this->currentSchoolYear();

        [$distribution, $distributionPct, $totalGraded] = $this->gradeDistribution($schoolYear);

        $recentGradeSubmissions = $this->recentGradeSubmissions();

        $notifications = $this->dashboardNotifications();

        return view('admin.dashboard', [
            'studentCount' => User::query()->where('role', 'student')->count(),
            'teacherCount' => User::query()->where('role', 'teacher')->count(),
            'subjectCount' => Subject::query()->count(),
            'sectionCount' => Section::query()->where('is_active', true)->count(),
            'schoolYearCount' => SchoolYear::query()->count(),
            'gradeRecordCount' => Grade::query()->count(),
            'assignmentCount' => TeacherSubject::query()
                ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
                ->count(),
            'recentStudents' => User::query()->where('role', 'student')->latest()->limit(5)->get(),
            'recentTeachers' => User::query()->where('role', 'teacher')->latest()->limit(5)->get(),
            'schoolYear' => $schoolYear,
            'gradeDistribution' => $distribution,
            'gradeDistributionPct' => $distributionPct,
            'totalGraded' => $totalGraded,
            'recentGradeSubmissions' => $recentGradeSubmissions,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Resolve the school year the dashboard should be scoped to.
     * Prefers the current one, then the most recent active, otherwise the latest.
     */
    private function currentSchoolYear(): ?SchoolYear
    {
        return SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::where('is_active', true)->latest()->first()
            ?? SchoolYear::latest()->first();
    }

    /**
     * Build the grade distribution buckets and percentages.
     *
     * @return array{0: array<string,int>, 1: array<string,int>, 2: int}
     */
    private function gradeDistribution(?SchoolYear $schoolYear): array
    {
        $gradeQuery = $this->gradedScope($schoolYear);

        $distribution = [
            '90-100' => (clone $gradeQuery)->whereBetween('final_grade', [90, 100])->count(),
            '85-89' => (clone $gradeQuery)->whereBetween('final_grade', [85, 89.99])->count(),
            '80-84' => (clone $gradeQuery)->whereBetween('final_grade', [80, 84.99])->count(),
            '75-79' => (clone $gradeQuery)->whereBetween('final_grade', [75, 79.99])->count(),
            'Below 75' => (clone $gradeQuery)->where('final_grade', '<', 75)->count(),
        ];

        $totalGraded = array_sum($distribution);

        $distributionPct = [];
        foreach ($distribution as $band => $count) {
            $distributionPct[$band] = $totalGraded > 0
                ? (int) round(($count / $totalGraded) * 100)
                : 0;
        }

        return [$distribution, $distributionPct, $totalGraded];
    }

    /**
     * Base query for grades that have a recorded final grade, scoped to the school year.
     */
    private function gradedScope(?SchoolYear $schoolYear): Builder
    {
        return Grade::query()
            ->whereNotNull('final_grade')
            ->when($schoolYear, function (Builder $q) use ($schoolYear) {
                $q->whereHas(
                    'teacherAssignment',
                    fn(Builder $sub) =>
                    $sub->where('school_year_id', $schoolYear->id)
                );
            });
    }

    /**
     * Most recent grade submissions for the dashboard preview.
     *
     * @return Collection<int, Grade>
     */
    private function recentGradeSubmissions(): Collection
    {
        return Grade::query()
            ->with(['studentUser', 'subject', 'teacher'])
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->limit(6)
            ->get();
    }

    /**
     * Build the merged notifications feed (grade + student events) shown in the navbar.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function dashboardNotifications(): Collection
    {
        return $this->gradeNotifications()
            ->merge($this->studentNotifications())
            ->sortByDesc('time')
            ->take(5)
            ->values()
            ->map(fn(array $notif) => $this->withHumanTime($notif));
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function gradeNotifications(): Collection
    {
        return Grade::query()
            ->with(['teacher', 'subject', 'studentUser'])
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->limit(4)
            ->get()
            ->map(fn(Grade $grade) => $this->gradeNotifPayload($grade));
    }

    /**
     * @return array<string, mixed>
     */
    private function gradeNotifPayload(Grade $grade): array
    {
        $teacher = $grade->teacher?->name ?? 'A teacher';
        $section = $grade->teacherAssignment?->section?->name ?? '';
        $subjectCode = $grade->subject?->code ?? '';
        $label = trim("{$subjectCode} {$section}");

        return [
            'type' => 'grade',
            'icon' => 'file-check-2',
            'tone' => 'info',
            'title' => "{$teacher} submitted grades" . ($label ? " for {$label}" : ''),
            'time' => $grade->submitted_at,
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function studentNotifications(): Collection
    {
        return User::query()
            ->where('role', 'student')
            ->latest('created_at')
            ->limit(3)
            ->get()
            ->map(fn(User $student) => [
                'type' => 'student',
                'icon' => 'user-plus',
                'tone' => 'success',
                'title' => "New student enrolled: {$student->name}",
                'time' => $student->created_at,
            ]);
    }

    /**
     * Add a human-readable "time ago" string to a notification payload.
     *
     * @param  array<string, mixed>  $notif
     * @return array<string, mixed>
     */
    private function withHumanTime(array $notif): array
    {
        $notif['time_human'] = $notif['time']
            ? Carbon::parse($notif['time'])->diffForHumans(null, true, true)
            : 'just now';

        return $notif;
    }
}