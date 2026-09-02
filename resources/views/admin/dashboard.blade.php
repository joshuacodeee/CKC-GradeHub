@extends('layouts.app', ['role' => 'admin', 'active' => 'dashboard', 'title' => 'Dashboard'])

@php
    $schoolYearName = $schoolYear?->name ?? 'All school years';
    $semester = $schoolYear?->semester ?? null;
    $totalStudents   = (int) $studentCount;
    $totalTeachers   = (int) $teacherCount;
    $totalSubjects   = (int) $subjectCount;
    $totalSections   = (int) $sectionCount;
    $totalAssignments = (int) $assignmentCount;
    $totalGrades     = (int) $gradeRecordCount;
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
                    <span class="w-10 h-px" style="background:var(--sg-brass-500)"></span>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color:var(--sg-brass-500)">
                        Admin command center
                    </p>
                </div>
                <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold leading-[1.05]">
                    School-wide view,<br>
                    <span style="color:var(--sg-brass-500)">term by term.</span>
                </h1>
                <p class="mt-4 text-white/70 text-sm sm:text-base max-w-xl leading-relaxed">
                    Manage students, teachers, sections, and grade submissions across the
                    entire Christ the King College community — all from one focused workspace.
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('admin.students') }}" class="sg-btn !px-4 !py-2 text-xs"
                        style="background:var(--sg-brass-500); color:var(--sg-navy-900)">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Add student
                    </a>
                    <a href="{{ route('admin.assignments.create') }}" class="sg-btn !px-4 !py-2 text-xs"
                        style="background:rgba(255,255,255,0.10); color:#fff; border:1px solid rgba(255,255,255,0.20)">
                        <i data-lucide="user-check" class="w-4 h-4"></i> Assign teacher
                    </a>
                    <a href="{{ route('admin.reports') }}" class="sg-btn !px-4 !py-2 text-xs"
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
                    <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-brass-500)">Grades recorded</p>
                    <p class="font-display text-xl font-bold mt-1">{{ number_format($totalGrades) }}</p>
                </div>
                <div class="rounded-2xl p-4" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.10);">
                    <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-brass-500)">Active assignments</p>
                    <p class="font-display text-xl font-bold mt-1">{{ number_format($totalAssignments) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ─────────── Stat strip ─────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @include('components.stat-card', ['label' => 'Students', 'value' => $totalStudents, 'icon' => 'users', 'color' => 'navy'])
        @include('components.stat-card', ['label' => 'Teachers', 'value' => $totalTeachers, 'icon' => 'graduation-cap', 'color' => 'brass'])
        @include('components.stat-card', ['label' => 'Subjects', 'value' => $totalSubjects, 'icon' => 'book-open', 'color' => 'success'])
        @include('components.stat-card', ['label' => 'Sections', 'value' => $totalSections, 'icon' => 'layers', 'color' => 'warning'])
    </div>

    {{-- ─────────── Main grid ─────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Grade distribution --}}
        <div class="sg-card p-5 xl:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="w-6 h-px" style="background:var(--sg-brass-500)"></span>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em]"
                            style="color:var(--sg-brass-600)">Distribution</p>
                    </div>
                    <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Grade Distribution</h3>
                    <p class="text-xs mt-0.5" style="color:var(--sg-text-muted)">
                        {{ $schoolYearName }} · {{ $totalGraded }} {{ \Illuminate\Support\Str::plural('grade', $totalGraded) }} recorded
                    </p>
                </div>
                <span class="sg-badge sg-badge-info hidden sm:inline-flex">All Sections</span>
            </div>

            @if ($totalGraded > 0)
                @php
                    $bandStyles = [
                        '90-100'   => ['bar' => 'var(--sg-success)', 'chip' => 'var(--sg-success-bg)', 'fg' => 'var(--sg-success)'],
                        '85-89'    => ['bar' => 'var(--sg-navy-500)', 'chip' => 'var(--sg-info-bg)',    'fg' => 'var(--sg-navy-600)'],
                        '80-84'    => ['bar' => 'var(--sg-brass-500)','chip' => 'var(--sg-warning-bg)', 'fg' => 'var(--sg-warning)'],
                        '75-79'    => ['bar' => '#a9761f',           'chip' => 'var(--sg-warning-bg)', 'fg' => 'var(--sg-warning)'],
                        'Below 75' => ['bar' => 'var(--sg-danger)',   'chip' => 'var(--sg-danger-bg)',  'fg' => 'var(--sg-danger)'],
                    ];
                @endphp
                <div class="space-y-3">
                    @foreach ($gradeDistribution as $band => $count)
                        @php
                            $pct = $gradeDistributionPct[$band] ?? 0;
                            $style = $bandStyles[$band] ?? $bandStyles['Below 75'];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold" style="color:var(--sg-text)">{{ $band }}</span>
                                <span style="color:var(--sg-text-muted)">
                                    {{ $count }} {{ \Illuminate\Support\Str::plural('student', $count) }} · {{ $pct }}%
                                </span>
                            </div>
                            <div class="h-2.5 rounded-full overflow-hidden" style="background:var(--sg-border)">
                                <div class="h-full rounded-full sg-transition"
                                    style="width: {{ max($pct, 2) }}%; background: {{ $style['bar'] }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-10 text-center text-sm" style="color:var(--sg-text-muted)">
                    Distribution will appear once grades are recorded.
                </div>
            @endif
        </div>

        {{-- Quick actions --}}
        <div class="sg-card p-5">
            <div class="flex items-center gap-3 mb-4">
                <span class="w-6 h-px" style="background:var(--sg-brass-500)"></span>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em]"
                    style="color:var(--sg-brass-600)">Shortcuts</p>
            </div>
            <h3 class="font-display font-semibold text-lg mb-1" style="color:var(--sg-text)">Quick Actions</h3>
            <p class="text-xs mb-4" style="color:var(--sg-text-muted)">Add new records or jump to reports.</p>
            <div class="space-y-2">
                <a href="{{ route('admin.students') }}" class="sg-btn sg-btn-outline w-full !justify-start !py-2.5">
                    <i data-lucide="user-plus" class="w-4 h-4" style="color:var(--sg-navy-600)"></i>
                    <span>Add Student</span>
                </a>
                <a href="{{ route('admin.teachers') }}" class="sg-btn sg-btn-outline w-full !justify-start !py-2.5">
                    <i data-lucide="graduation-cap" class="w-4 h-4" style="color:var(--sg-brass-600)"></i>
                    <span>Add Teacher</span>
                </a>
                <a href="{{ route('admin.subjects') }}" class="sg-btn sg-btn-outline w-full !justify-start !py-2.5">
                    <i data-lucide="book-plus" class="w-4 h-4" style="color:var(--sg-success)"></i>
                    <span>Add Subject</span>
                </a>
                <a href="{{ route('admin.sections.create') }}" class="sg-btn sg-btn-outline w-full !justify-start !py-2.5">
                    <i data-lucide="layers" class="w-4 h-4" style="color:var(--sg-warning)"></i>
                    <span>Add Section</span>
                </a>
                <a href="{{ route('admin.assignments.create') }}" class="sg-btn sg-btn-outline w-full !justify-start !py-2.5">
                    <i data-lucide="user-check" class="w-4 h-4" style="color:var(--sg-navy-600)"></i>
                    <span>Assign Teacher</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ─────────── Lower grid ─────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Recent grade submissions --}}
        <div class="sg-card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="w-6 h-px" style="background:var(--sg-brass-500)"></span>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em]"
                            style="color:var(--sg-brass-600)">Activity</p>
                    </div>
                    <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Recent Submissions</h3>
                </div>
                <a href="{{ route('admin.reports') }}" class="text-xs font-semibold hover:opacity-80"
                    style="color:var(--sg-navy-600)">View all →</a>
            </div>

            @forelse ($recentGradeSubmissions as $grade)
                @php
                    $studentName = $grade->studentUser?->name ?? '—';
                    $studentInitials = collect(explode(' ', $studentName))->filter()->take(2)
                        ->map(fn ($p) => strtoupper(substr($p, 0, 1)))->join('');
                    $subjectCode = $grade->subject?->code ?? '—';
                    $subjectName = $grade->subject?->name ?? '';
                    $teacherName = $grade->teacher?->name ?? '—';
                    $eq = $grade->final_grade !== null
                        ? \App\Support\GradingScale::equivalent((float) $grade->final_grade)
                        : null;
                    $finalEq = $eq !== null ? number_format($eq, 1) : '—';
                    $submittedAt = $grade->submitted_at ?? $grade->updated_at;
                    $remarks = $grade->remarks;
                    $gradeTone = match (strtolower((string) $remarks)) {
                        'passed'   => 'success',
                        'failed'   => 'danger',
                        'inc'      => 'warning',
                        default    => 'info',
                    };
                @endphp
                <div class="flex items-center gap-3 py-3 border-b last:border-0" style="border-color:var(--sg-border)">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 font-display font-bold text-xs"
                        style="background:var(--sg-navy-50); color:var(--sg-navy-700)">
                        {{ $studentInitials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold truncate" style="color:var(--sg-text)">
                            {{ $studentName }}
                            <span class="font-normal" style="color:var(--sg-text-muted)">·</span>
                            <span class="font-normal" style="color:var(--sg-text-muted)">{{ $subjectCode }}</span>
                        </p>
                        <p class="text-xs truncate mt-0.5" style="color:var(--sg-text-muted)">
                            Submitted by {{ $teacherName }}
                            <span style="color:var(--sg-text-muted)">·</span>
                            {{ $submittedAt ? $submittedAt->diffForHumans(null, true, true) . ' ago' : '—' }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-display font-bold text-lg" style="color:var(--sg-navy-700)">{{ $finalEq }}</p>
                        @if ($remarks)
                            <span class="sg-badge sg-badge-{{ $gradeTone }} !text-[10px] !py-0.5">{{ ucfirst($remarks) }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-sm" style="color:var(--sg-text-muted)">
                    No grade submissions yet.
                </div>
            @endforelse
        </div>

        {{-- Recently added students --}}
        <div class="sg-card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="w-6 h-px" style="background:var(--sg-brass-500)"></span>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em]"
                            style="color:var(--sg-brass-600)">Roster</p>
                    </div>
                    <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Recently Added</h3>
                </div>
                <a href="{{ route('admin.students') }}" class="text-xs font-semibold hover:opacity-80"
                    style="color:var(--sg-navy-600)">View all →</a>
            </div>
            @forelse ($recentStudents as $student)
                @php
                    $initials = collect(explode(' ', $student->name))->filter()->take(2)
                        ->map(fn ($p) => strtoupper(substr($p, 0, 1)))->join('');
                @endphp
                <div class="flex items-center gap-3 py-3 border-b last:border-0" style="border-color:var(--sg-border)">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 font-display font-bold text-xs"
                        style="background:linear-gradient(140deg, var(--sg-brass-500), var(--sg-brass-600)); color:var(--sg-navy-900)">
                        {{ $initials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold truncate" style="color:var(--sg-text)">{{ $student->name }}</p>
                        <p class="text-xs truncate" style="color:var(--sg-text-muted)">
                            {{ $student->student_id }}
                            @if ($student->course)
                                <span style="color:var(--sg-text-muted)">·</span> {{ $student->course }}
                            @endif
                        </p>
                    </div>
                    <span class="text-xs flex-shrink-0" style="color:var(--sg-text-muted)">
                        {{ $student->created_at->format('M d') }}
                    </span>
                </div>
            @empty
                <div class="py-10 text-center text-sm" style="color:var(--sg-text-muted)">
                    No student accounts yet.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
