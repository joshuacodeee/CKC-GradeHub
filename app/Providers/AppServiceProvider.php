<?php

namespace App\Providers;

use App\Models\Grade;
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
        // Share dynamic notifications with the global navbar.
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

            $view->with('navbarNotifications', $notifications);
        });
    }
}
