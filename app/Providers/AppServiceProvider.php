<?php

namespace App\Providers;

use App\Models\Grade;
use App\Models\NotificationRead;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share dynamic notifications with the global navbar. The
        // composer receives an Illuminate\View\View instance, NOT the
        // View facade — using the wrong type-hint here produced an
        // "Argument #1 ($view) must be of type
        // Illuminate\Support\Facades\View, Illuminate\View\View given"
        // error on every page that included components.navbar.
        View::composer('components.navbar', function (ViewInstance $view) {
            $user = auth()->user();
            $notifications = collect();

            if ($user) {
                if ($user->role === 'admin') {
                    $gradeNotifs = Grade::query()
                        ->with(['teacher', 'subject'])
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
                                'id'    => 'grade-' . $grade->id,
                                'icon'  => 'file-check-2',
                                'tone'  => 'info',
                                'title' => "{$teacher} submitted grades" . ($label ? " for {$label}" : ''),
                                'time'  => $grade->submitted_at,
                            ];
                        });

                    $studentNotifs = User::query()
                        ->where('role', 'student')
                        ->latest('created_at')
                        ->limit(3)
                        ->get()
                        ->map(function (User $student) {
                            return [
                                'id'    => 'student-' . $student->id,
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
                } elseif ($user->role === 'teacher') {
                    $notifications = Grade::query()
                        ->where('teacher_id', $user->id)
                        ->with(['studentUser', 'subject'])
                        ->whereNotNull('submitted_at')
                        ->orderByDesc('submitted_at')
                        ->limit(5)
                        ->get()
                        ->map(function (Grade $grade) {
                            $student = $grade->studentUser?->name ?? 'A student';
                            $subjectCode = $grade->subject?->code ?? '';
                            return [
                                'id'    => 'grade-' . $grade->id,
                                'icon'  => 'file-check-2',
                                'tone'  => 'info',
                                'title' => "Grade for {$student}" . ($subjectCode ? " ({$subjectCode})" : '') . ' recorded',
                                'time'  => $grade->submitted_at,
                            ];
                        })
                        ->map(function (array $notif) {
                            $notif['time_human'] = $notif['time']
                                ? Carbon::parse($notif['time'])->diffForHumans(null, true, true)
                                : 'just now';
                            return $notif;
                        });
                } elseif ($user->role === 'student') {
                    $notifications = Grade::query()
                        ->where('student_id', $user->id)
                        ->with('subject')
                        ->whereNotNull('submitted_at')
                        ->orderByDesc('submitted_at')
                        ->limit(5)
                        ->get()
                        ->map(function (Grade $grade) {
                            $subjectCode = $grade->subject?->code ?? 'a subject';
                            $final = $grade->final_grade !== null ? number_format((float) $grade->final_grade, 2) : '—';
                            return [
                                'id'    => 'grade-' . $grade->id,
                                'icon'  => 'file-check-2',
                                'tone'  => 'info',
                                'title' => "New grade posted in {$subjectCode}: {$final}",
                                'time'  => $grade->submitted_at,
                            ];
                        })
                        ->map(function (array $notif) {
                            $notif['time_human'] = $notif['time']
                                ? Carbon::parse($notif['time'])->diffForHumans(null, true, true)
                                : 'just now';
                            return $notif;
                        });
                }
            }

            // Look up which of these notifications the current user has already read.
            $readIds = collect();
            if ($user && $notifications->isNotEmpty()) {
                $ids = $notifications->pluck('id')->filter()->all();
                $readIds = NotificationRead::where('user_id', $user->id)
                    ->whereIn('notification_id', $ids)
                    ->pluck('notification_id');
            }

            $notifications = $notifications->map(function (array $notif) use ($readIds) {
                $notif['read'] = $readIds->contains($notif['id'] ?? null);
                return $notif;
            });

            $unreadCount = $notifications->where('read', false)->count();

            $view->with([
                'navbarNotifications' => $notifications,
                'navbarUnreadCount'   => $unreadCount,
            ]);
        });

        // Share live platform stats with the auth pages (login + register).
        View::composer(['auth.login', 'auth.register'], function (ViewInstance $view) {
            $schoolYear = SchoolYear::where('is_current', true)->first()
                ?? SchoolYear::where('is_active', true)->latest()->first()
                ?? SchoolYear::latest()->first();

            $studentCount  = User::query()->where('role', 'student')->count();
            $teacherCount  = User::query()->where('role', 'teacher')->count();
            $subjectCount  = Subject::query()->count();
            $sectionCount  = Section::query()->where('is_active', true)->count();
            $gradeCount    = Grade::query()->whereNotNull('final_grade')->count();

            $gradesThisWeek = Grade::query()
                ->whereNotNull('submitted_at')
                ->where('submitted_at', '>=', now()->subWeek())
                ->count();

            $weightedAverage = Grade::query()
                ->whereNotNull('final_grade')
                ->when($schoolYear, function ($q) use ($schoolYear) {
                    $q->whereHas('teacherAssignment', fn ($sub) => $sub->where('school_year_id', $schoolYear->id));
                })
                ->avg('final_grade');

            // Top student by average of recorded final grades (only if there is data).
            $topStudent = null;
            if ($gradeCount > 0) {
                $row = Grade::query()
                    ->selectRaw('student_id, AVG(final_grade) as avg_grade, COUNT(*) as grade_count')
                    ->whereNotNull('final_grade')
                    ->whereNotNull('student_id')
                    ->when($schoolYear, function ($q) use ($schoolYear) {
                        $q->whereHas('teacherAssignment', fn ($sub) => $sub->where('school_year_id', $schoolYear->id));
                    })
                    ->groupBy('student_id')
                    ->orderByDesc('avg_grade')
                    ->with('studentUser')
                    ->first();

                if ($row && $row->studentUser) {
                    $student = $row->studentUser;
                    $sectionName = $student->section?->name ?? null;
                    $topStudent = [
                        'name'        => $student->name,
                        'section'     => $sectionName,
                        'student_id'  => $student->student_id,
                        'course'      => $student->course,
                        'year_level'  => $student->year_level,
                        'block'       => $student->block,
                        'average'     => round((float) $row->avg_grade, 1),
                        'grade_count' => (int) $row->grade_count,
                    ];
                }
            }

            $view->with([
                'authSchoolYear'        => $schoolYear,
                'authStudentCount'      => $studentCount,
                'authTeacherCount'      => $teacherCount,
                'authSubjectCount'      => $subjectCount,
                'authSectionCount'      => $sectionCount,
                'authGradeCount'        => $gradeCount,
                'authGradesThisWeek'    => $gradesThisWeek,
                'authWeightedAverage'   => $weightedAverage,
                'authTopStudent'        => $topStudent,
            ]);
        });
    }
}
