@extends('layouts.app', ['role' => 'teacher', 'active' => 'dashboard', 'title' => 'Dashboard'])

@php
    $user = auth()->user();
    $teacherName = $user?->name ?? 'Teacher';
    $teacherInitials = collect(explode(' ', $teacherName))->filter()->take(2)
        ->map(fn ($p) => strtoupper(substr($p, 0, 1)))->join('');
    $honorific = 'Mr./Ms.';
    $classCount = $assignedClasses->count();
    $schoolYearName = $schoolYear?->name ?? 'All terms';
    $semester = $schoolYear?->semester ?? null;
    $totalGradedAcross = $classRows->sum('submitted');
    $overallPct = $classRows->sum('total') > 0
        ? (int) round(($classRows->sum('submitted') / $classRows->sum('total')) * 100)
        : 0;
@endphp

@section('content')
<div class="space-y-6">

    {{-- ─────────── Hero banner ─────────── --}}
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8"
        style="background:linear-gradient(140deg, var(--sg-navy-900), var(--sg-navy-700)); color:#fff;">
        <div class="absolute -right-24 -top-24 w-72 h-72 rounded-full opacity-20 pointer-events-none"
            style="background: var(--sg-brass-500); filter: blur(40px);"></div>
        <div class="absolute -left-12 -bottom-16 w-72 h-72 rounded-full opacity-15 pointer-events-none"
            style="background: var(--sg-navy-500); filter: blur(40px);"></div>

        <div class="relative grid lg:grid-cols-[1.4fr_1fr] gap-6 lg:gap-10 items-center">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-display font-bold tracking-wider"
                        style="background:linear-gradient(140deg, var(--sg-brass-500), var(--sg-brass-600)); color:var(--sg-navy-900)">
                        {{ $teacherInitials }}
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.22em]" style="color:var(--sg-brass-500)">Welcome back</p>
                        <p class="text-sm text-white/60">CKC GradeHub · Faculty workspace</p>
                    </div>
                </div>
                <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold leading-[1.05]">
                    {{ $honorific }} {{ $teacherName }},<br>
                    <span style="color:var(--sg-brass-500)">let's close the term.</span>
                </h1>
                <p class="mt-4 text-white/70 text-sm sm:text-base max-w-xl leading-relaxed">
                    @if ($classCount > 0)
                        You have <strong>{{ $classCount }}</strong>
                        {{ \Illuminate\Support\Str::plural('class', $classCount) }} this term
                        @if ($pendingGrades > 0)
                            and <strong>{{ $pendingGrades }}</strong>
                            {{ \Illuminate\Support\Str::plural('grade', $pendingGrades) }} waiting to be submitted.
                        @else
                            and everything is on track.
                        @endif
                    @else
                        No active classes assigned to you this term yet.
                    @endif
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    @if ($classCount > 0)
                        @php $firstClass = $assignedClasses->first(); @endphp
                        <a href="{{ route('teacher.grades.enter', $firstClass) }}"
                            class="sg-btn !px-4 !py-2 text-xs"
                            style="background:var(--sg-brass-500); color:var(--sg-navy-900)">
                            <i data-lucide="square-pen" class="w-4 h-4"></i> Enter grades
                        </a>
                    @endif
                    <a href="{{ route('teacher.classes') }}" class="sg-btn !px-4 !py-2 text-xs"
                        style="background:rgba(255,255,255,0.10); color:#fff; border:1px solid rgba(255,255,255,0.20)">
                        <i data-lucide="presentation" class="w-4 h-4"></i> My classes
                    </a>
                    <a href="{{ route('teacher.reports') }}" class="sg-btn !px-4 !py-2 text-xs"
                        style="background:rgba(255,255,255,0.10); color:#fff; border:1px solid rgba(255,255,255,0.20)">
                        <i data-lucide="file-bar-chart-2" class="w-4 h-4"></i> Reports
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl p-4" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.10);">
                    <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-brass-500)">School year</p>
                    <p class="font-display text-xl font-bold mt-1">{{ $schoolYearName }}</p>
                </div>
                <div class="rounded-2xl p-4" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.10);">
                    <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-brass-500)">Semester</p>
                    <p class="font-display text-xl font-bold mt-1">{{ $semester ?? '—' }}</p>
                </div>
                <div class="rounded-2xl p-4" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.10);">
                    <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-brass-500)">Total students</p>
                    <p class="font-display text-xl font-bold mt-1">{{ number_format($totalStudents) }}</p>
                </div>
                <div class="rounded-2xl p-4" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.10);">
                    <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-brass-500)">Submission rate</p>
                    <p class="font-display text-xl font-bold mt-1">{{ $overallPct }}%</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ─────────── Pending alert ─────────── --}}
    @if ($pendingGrades > 0)
        <div class="rounded-2xl p-4 sm:p-5 flex items-start gap-3 border"
            style="background:var(--sg-warning-bg); border-color: var(--sg-warning) !important;">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background:rgba(181,106,5,0.15)">
                <i data-lucide="clock-alert" class="w-5 h-5" style="color:var(--sg-warning)"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold" style="color:var(--sg-text)">
                    {{ $pendingGrades }} {{ \Illuminate\Support\Str::plural('grade', $pendingGrades) }} pending submission
                </p>
                <p class="text-sm mt-0.5" style="color:var(--sg-text-muted)">
                    Review and submit your draft grades to make them visible to students and admins.
                </p>
            </div>
            @if ($classCount > 0)
                <a href="{{ route('teacher.grades.enter', $assignedClasses->first()) }}"
                    class="sg-btn sg-btn-outline !py-1.5 !text-xs flex-shrink-0">
                    Review
                </a>
            @endif
        </div>
    @endif

    {{-- ─────────── Stat strip ─────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @include('components.stat-card', ['label' => 'Assigned Classes', 'value' => $classCount, 'icon' => 'presentation', 'color' => 'navy'])
        @include('components.stat-card', ['label' => 'Total Students', 'value' => $totalStudents, 'icon' => 'users', 'color' => 'brass'])
        @include('components.stat-card', ['label' => 'Pending Grades', 'value' => $pendingGrades, 'icon' => 'clock', 'color' => 'warning'])
        @include('components.stat-card', ['label' => 'Submitted', 'value' => $totalGradedAcross, 'icon' => 'file-check-2', 'color' => 'success'])
    </div>

    {{-- ─────────── Main grid ─────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- My Classes --}}
        <div class="sg-card p-5 xl:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="w-6 h-px" style="background:var(--sg-brass-500)"></span>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em]"
                            style="color:var(--sg-brass-600)">Teaching load</p>
                    </div>
                    <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">My Classes</h3>
                </div>
                <a href="{{ route('teacher.classes') }}" class="text-xs font-semibold hover:opacity-80"
                    style="color:var(--sg-navy-600)">View all →</a>
            </div>

            @forelse ($classRows as $row)
                @php
                    $class = $row;
                    $subject = $row['subject'];
                    $section = $row['section'];
                    $pct = $row['pct'];
                    $avgEq = $row['average'] !== null
                        ? \App\Support\GradingScale::equivalent((float) $row['average'])
                        : null;
                    $status = match (true) {
                        $pct === 100 => ['label' => 'Complete', 'tone' => 'success'],
                        $pct > 0     => ['label' => 'In progress', 'tone' => 'warning'],
                        default      => ['label' => 'Not started', 'tone' => 'info'],
                    };
                @endphp
                <div class="rounded-2xl p-4 mb-3 last:mb-0 border" style="border-color:var(--sg-border)">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="sg-badge"
                                    style="background:var(--sg-info-bg); color:var(--sg-navy-600)">
                                    {{ $subject?->code ?? '—' }}
                                </span>
                                <span class="sg-badge sg-badge-{{ $status['tone'] }}">{{ $status['label'] }}</span>
                            </div>
                            <p class="font-display font-semibold mt-2" style="color:var(--sg-text)">
                                {{ $subject?->name ?? 'Unnamed subject' }}
                            </p>
                            <p class="text-xs mt-0.5" style="color:var(--sg-text-muted)">
                                {{ $section?->name ?? '—' }}
                                @if ($section?->course) · {{ $section->course }} @endif
                                @if ($section?->year_level) · {{ $section->year_level }} @endif
                            </p>
                        </div>
                        <a href="{{ route('teacher.grades.enter', $row['id']) }}"
                            class="sg-btn sg-btn-primary !py-1.5 !text-xs flex-shrink-0">
                            <i data-lucide="square-pen" class="w-3.5 h-3.5"></i> Enter
                        </a>
                    </div>

                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span style="color:var(--sg-text-muted)">
                            {{ $row['submitted'] }}/{{ $row['total'] }} grades
                            @if ($row['average'] !== null)
                                · Class avg <strong style="color:var(--sg-text)">{{ number_format((float) $row['average'], 2) }}</strong>
                                <span style="color:var(--sg-navy-700); font-weight:600;">
                                    ({{ $avgEq !== null ? number_format($avgEq, 1) : '—' }})
                                </span>
                            @endif
                        </span>
                        <span class="font-semibold" style="color:var(--sg-text)">{{ $pct }}%</span>
                    </div>
                    <div class="h-1.5 rounded-full overflow-hidden" style="background:var(--sg-border)">
                        <div class="h-full rounded-full sg-transition"
                            style="width: {{ max($pct, 2) }}%; background: {{ $pct === 100 ? 'var(--sg-success)' : ($pct > 0 ? 'var(--sg-warning)' : 'var(--sg-border)') }};"></div>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center">
                    <div class="w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center"
                        style="background:var(--sg-info-bg)">
                        <i data-lucide="presentation" class="w-6 h-6" style="color:var(--sg-navy-600)"></i>
                    </div>
                    <p class="text-sm font-medium" style="color:var(--sg-text)">No classes assigned</p>
                    <p class="text-xs mt-1" style="color:var(--sg-text-muted)">
                        Once the admin assigns you a subject, it will appear here.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Right column: teacher profile card + recent activity --}}
        <div class="space-y-6">

            <div class="sg-card p-5">
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-6 h-px" style="background:var(--sg-brass-500)"></span>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em]"
                        style="color:var(--sg-brass-600)">Faculty</p>
                </div>
                <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Quick info</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <dt style="color:var(--sg-text-muted)">Name</dt>
                        <dd class="font-semibold text-right truncate ml-3" style="color:var(--sg-text)">{{ $teacherName }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt style="color:var(--sg-text-muted)">Email</dt>
                        <dd class="text-right truncate ml-3" style="color:var(--sg-text)">{{ $user?->email ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt style="color:var(--sg-text-muted)">Classes</dt>
                        <dd class="font-semibold" style="color:var(--sg-text)">{{ $classCount }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt style="color:var(--sg-text-muted)">Students</dt>
                        <dd class="font-semibold" style="color:var(--sg-text)">{{ number_format($totalStudents) }}</dd>
                    </div>
                </dl>
                <a href="{{ route('teacher.profile') }}" class="sg-btn sg-btn-outline w-full mt-4 !py-2 !text-xs">
                    <i data-lucide="user" class="w-4 h-4"></i> View profile
                </a>
            </div>

            <div class="sg-card p-5">
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-6 h-px" style="background:var(--sg-brass-500)"></span>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em]"
                        style="color:var(--sg-brass-600)">Activity</p>
                </div>
                <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Recent grades</h3>

                <ul class="mt-3 space-y-3">
                    @forelse ($recentGrades as $g)
                        @php
                            $initials = collect(explode(' ', $g->studentUser?->name ?? ''))->filter()->take(2)
                                ->map(fn ($p) => strtoupper(substr($p, 0, 1)))->join('');
                            $eq = $g->final_grade !== null
                                ? \App\Support\GradingScale::equivalent((float) $g->final_grade)
                                : null;
                            $finalEq = $eq !== null ? number_format($eq, 1) : '—';
                        @endphp
                        <li class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 font-display font-bold text-xs"
                                style="background:var(--sg-navy-50); color:var(--sg-navy-700)">
                                {{ $initials ?: '?' }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium truncate" style="color:var(--sg-text)">
                                    {{ $g->studentUser?->name ?? '—' }}
                                </p>
                                <p class="text-xs truncate" style="color:var(--sg-text-muted)">
                                    {{ $g->subject?->code ?? '—' }}
                                    <span style="color:var(--sg-text-muted)">·</span>
                                    {{ $g->updated_at?->diffForHumans(null, true, true) }} ago
                                </p>
                            </div>
                            <span class="font-display font-bold text-sm" style="color:var(--sg-navy-700)">{{ $finalEq }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-center py-4" style="color:var(--sg-text-muted)">
                            No recent grade activity.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
