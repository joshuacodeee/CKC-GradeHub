@extends('layouts.app', ['role' => 'student', 'active' => 'dashboard', 'title' => 'Dashboard'])

@php
    $user = auth()->user();
    $studentName = $user?->name ?? 'Student';
    $studentId = $user?->student_id ?? '—';
    $course = $user?->course ?? '—';
    $yearLevel = $user?->year_level ?? '—';
    $block = $user?->block;
    $studentInitials = collect(explode(' ', $studentName))->filter()->take(2)
        ->map(fn($p) => strtoupper(substr($p, 0, 1)))->join('');
    $schoolYearName = $schoolYear?->name ?? date('Y') . '–' . (date('Y') + 1);
    $semester = $schoolYear?->semester ?? 'Current Term';
    $gwaDisplay = $gwa !== null ? number_format($gwa, 2) : '—';
    $enrolledCount = (int) ($enrolledCount ?? 0);
    $totalUnits = (int) $totalUnits;
    $gradedCount = (int) ($gradedCount ?? 0);
    $standing = $standing ?? 'No grades yet';
    $standingTone = $standingTone ?? 'info';
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
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-display font-bold text-lg tracking-wider"
                            style="background:linear-gradient(140deg, var(--sg-brass-500), var(--sg-brass-600)); color:var(--sg-navy-900)"
                            data-current-user-initials>
                            {{ $studentInitials }}
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.22em]" style="color:var(--sg-brass-500)">
                                Student dashboard
                            </p>
                            <p class="text-sm text-white/60">CKC GradeHub · Personal workspace</p>
                        </div>
                    </div>
                    <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold leading-[1.05]"
                        data-current-user-name>
                        Welcome back,<br>
                        <span style="color:var(--sg-brass-500)">{{ $studentName }}.</span>
                    </h1>
                    <p class="mt-4 text-white/70 text-sm sm:text-base max-w-xl leading-relaxed">
                        Your academic record at a glance — grades, subjects, and standing for the
                        <strong>{{ $semester }}</strong> of School Year <strong>{{ $schoolYearName }}</strong>.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <a href="{{ route('student.grades') }}" class="sg-btn !px-4 !py-2 text-xs"
                            style="background:var(--sg-brass-500); color:var(--sg-navy-900)">
                            <i data-lucide="file-check-2" class="w-4 h-4"></i> View grades
                        </a>
                        <a href="{{ route('student.subjects') }}" class="sg-btn !px-4 !py-2 text-xs"
                            style="background:rgba(255,255,255,0.10); color:#fff; border:1px solid rgba(255,255,255,0.20)">
                            <i data-lucide="book-open" class="w-4 h-4"></i> My subjects
                        </a>
                        <a href="{{ route('student.records') }}" class="sg-btn !px-4 !py-2 text-xs"
                            style="background:rgba(255,255,255,0.10); color:#fff; border:1px solid rgba(255,255,255,0.20)">
                            <i data-lucide="scroll-text" class="w-4 h-4"></i> Records
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl p-4"
                        style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.10);">
                        <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-brass-500)">Student ID</p>
                        <p class="font-display text-xl font-bold mt-1 truncate">{{ $studentId }}</p>
                    </div>
                    <div class="rounded-2xl p-4"
                        style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.10);">
                        <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-brass-500)">Year & Block</p>
                        <p class="font-display text-xl font-bold mt-1 truncate">
                            {{ $yearLevel }}{{ $block ? ' · ' . $block : '' }}
                        </p>
                    </div>
                    <div class="rounded-2xl p-4 col-span-2"
                        style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.10);">
                        <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-brass-500)">Course</p>
                        <p class="font-display text-base font-bold mt-1 truncate">{{ $course }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────── Stat strip ─────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @include('components.stat-card', [
                'label' => 'General Weighted Average',
                'value' => $gwaDisplay,
                'icon' => 'trending-up',
                'color' => 'navy',
            ])
            @include('components.stat-card', [
                'label' => 'Enrolled Subjects',
                'value' => $enrolledCount,
                'icon' => 'book-open',
                'color' => 'brass',
            ])
            @include('components.stat-card', [
                'label' => 'Units This Term',
                'value' => $totalUnits,
                'icon' => 'layers',
                'color' => 'success',
            ])
            @include('components.stat-card', [
                'label' => 'Standing',
                'value' => $standing,
                'icon' => 'award',
                'color' => $standingTone,
            ])
        </div>

        {{-- ─────────── Main grid ─────────── --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Grade summary --}}
            <div class="sg-card p-5 xl:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="w-6 h-px" style="background:var(--sg-brass-500)"></span>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em]"
                                style="color:var(--sg-brass-600)">Term grades</p>
                        </div>
                        <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Grade Summary</h3>
                        <p class="text-xs mt-0.5" style="color:var(--sg-text-muted)">
                            {{ $gradedCount }} of {{ $enrolledCount }}
                            {{ \Illuminate\Support\Str::plural('subject', $enrolledCount) }} graded
                        </p>
                    </div>
                    <a href="{{ route('student.grades') }}" class="text-xs font-semibold hover:opacity-80"
                        style="color:var(--sg-navy-600)">Full grade sheet →</a>
                </div>

                <div class="overflow-x-auto no-print">
                    <table class="sg-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th class="text-center">Prelim (Eq.)</th>
                                <th class="text-center">Midterm (Eq.)</th>
                                <th class="text-center">Final (Eq.)</th>
                                <th class="text-center">Equivalent</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($grades as $grade)
                                @php
                                    $prelimEq  = $grade->prelim  !== null ? \App\Support\GradingScale::equivalent((float) $grade->prelim)  : null;
                                    $midtermEq = $grade->midterm !== null ? \App\Support\GradingScale::equivalent((float) $grade->midterm) : null;
                                    $finalEq   = $grade->final   !== null ? \App\Support\GradingScale::equivalent((float) $grade->final)   : null;
                                @endphp
                                <tr>
                                    <td class="font-medium" style="color:var(--sg-text)">
                                        <div class="flex flex-col">
                                            <span>{{ $grade->subject?->code ?? '—' }}</span>
                                            <span class="text-xs font-normal" style="color:var(--sg-text-muted)">
                                                {{ $grade->subject?->name ?? '' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center font-semibold" style="color:var(--sg-navy-700)">
                                        {{ $prelimEq !== null ? number_format($prelimEq, 1) : '—' }}
                                    </td>
                                    <td class="text-center font-semibold" style="color:var(--sg-navy-700)">
                                        {{ $midtermEq !== null ? number_format($midtermEq, 1) : '—' }}
                                    </td>
                                    <td class="text-center font-semibold" style="color:var(--sg-navy-700)">
                                        {{ $finalEq !== null ? number_format($finalEq, 1) : '—' }}
                                    </td>
                                    <td class="text-center font-semibold" style="color:var(--sg-navy-700)">
                                        {{ $grade->grade_equivalent !== null ? number_format($grade->grade_equivalent, 1) : '—' }}
                                    </td>
                                    <td>
                                        @if($grade->remarks === 'Passed')
                                            <span class="sg-badge sg-badge-success">Passed</span>
                                        @elseif($grade->remarks === 'Failed')
                                            <span class="sg-badge sg-badge-danger">Failed</span>
                                        @else
                                            <span class="sg-badge sg-badge-info">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-10" style="color:var(--sg-text-muted)">
                                        No grades recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right column: my subjects + latest grade --}}
            <div class="space-y-6">

                <div class="sg-card p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-6 h-px" style="background:var(--sg-brass-500)"></span>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em]" style="color:var(--sg-brass-600)">
                            Latest</p>
                    </div>
                    <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Newest Grade</h3>
                    @if ($latestGrade)
                        @php
                            $lgEq = $latestGrade->final_grade !== null
                                ? \App\Support\GradingScale::equivalent((float) $latestGrade->final_grade)
                                : null;
                            $lgFinalEq = $lgEq !== null ? number_format($lgEq, 1) : '—';
                        @endphp
                        <div class="mt-4 rounded-2xl p-4 border" style="border-color:var(--sg-border)">
                            <p class="text-xs uppercase tracking-wider" style="color:var(--sg-text-muted)">
                                {{ $latestGrade->subject?->code ?? '—' }}
                            </p>
                            <p class="font-display font-semibold mt-1" style="color:var(--sg-text)">
                                {{ $latestGrade->subject?->name ?? 'Subject' }}
                            </p>
                            <div class="flex items-end justify-between mt-3">
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-text-muted)">Final
                                        grade (eq.)</p>
                                    <p class="font-display font-bold text-3xl" style="color:var(--sg-navy-700)">{{ $lgFinalEq }}
                                    </p>
                                </div>
                                @if ($latestGrade->updated_at)
                                    <p class="text-xs text-right" style="color:var(--sg-text-muted)">
                                        Updated<br>
                                        <span
                                            style="color:var(--sg-text)">{{ $latestGrade->updated_at->diffForHumans(null, true, true) }}
                                            ago</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="mt-4 text-center py-6 text-sm" style="color:var(--sg-text-muted)">
                            No grades posted yet.
                        </div>
                    @endif
                </div>

                <div class="sg-card p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-6 h-px" style="background:var(--sg-brass-500)"></span>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em]" style="color:var(--sg-brass-600)">
                            My subjects</p>
                    </div>
                    <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Enrolled This Term</h3>

                    <ul class="mt-3 space-y-2">
                        @forelse ($enrolledSubjects as $subject)
                            <li class="flex items-center justify-between gap-3 py-2 px-3 rounded-lg"
                                style="background:var(--sg-bg)">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold truncate" style="color:var(--sg-text)">
                                        {{ $subject->code }}
                                    </p>
                                    <p class="text-xs truncate" style="color:var(--sg-text-muted)">{{ $subject->name }}</p>
                                </div>
                                <span class="text-xs flex-shrink-0" style="color:var(--sg-text-muted)">
                                    {{ $subject->units ?? 0 }}
                                    {{ \Illuminate\Support\Str::plural('unit', (int) $subject->units) }}
                                </span>
                            </li>
                        @empty
                            <li class="text-sm text-center py-4" style="color:var(--sg-text-muted)">
                                No subjects enrolled this term.
                            </li>
                        @endforelse
                    </ul>
                    <a href="{{ route('student.subjects') }}" class="sg-btn sg-btn-outline w-full mt-4 !py-2 !text-xs">
                        <i data-lucide="book-open" class="w-4 h-4"></i> View all subjects
                    </a>
                </div>
            </div>
        </div>

    </div>
@endsection