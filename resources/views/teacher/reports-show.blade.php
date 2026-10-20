@extends('layouts.app', ['role' => 'teacher', 'active' => 'reports', 'title' => 'Class Grade Report'])

@php
    $subject = $assignment->subject;
    $section = $assignment->section;
    $teacher = $assignment->teacher;
    $sy = $assignment->schoolYear;
    $gradeMap = [
        '90-100' => ['bar' => 'var(--sg-success)', 'chip' => 'var(--sg-success-bg)', 'fg' => 'var(--sg-success)'],
        '85-89' => ['bar' => 'var(--sg-navy-500)', 'chip' => 'var(--sg-info-bg)', 'fg' => 'var(--sg-navy-600)'],
        '80-84' => ['bar' => 'var(--sg-brass-500)', 'chip' => 'var(--sg-warning-bg)', 'fg' => 'var(--sg-warning)'],
        '75-79' => ['bar' => '#a9761f', 'chip' => 'var(--sg-warning-bg)', 'fg' => 'var(--sg-warning)'],
        'Below 75' => ['bar' => 'var(--sg-danger)', 'chip' => 'var(--sg-danger-bg)', 'fg' => 'var(--sg-danger)'],
    ];
    $totalDist = array_sum($distribution);
@endphp

@section('content')
    <div class="space-y-5">

        {{-- Breadcrumb / back --}}
        <div class="flex items-center justify-between gap-2 text-sm no-print">
            <a href="{{ route('teacher.reports') }}" class="inline-flex items-center gap-1.5 hover:opacity-80"
                style="color:var(--sg-text-muted)">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to My Reports
            </a>
            <div class="flex gap-2">
                <a href="{{ route('teacher.grades.enter', $assignment) }}" class="sg-btn sg-btn-outline">
                    <i data-lucide="square-pen" class="w-4 h-4"></i> Edit Grades
                </a>
                <a href="{{ route('teacher.reports.print', $assignment) }}" target="_blank" rel="noopener"
                    class="sg-btn sg-btn-outline">
                    <i data-lucide="printer" class="w-4 h-4"></i> Print
                </a>
            </div>
        </div>

        {{-- Header --}}
        <div class="sg-card p-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="sg-badge"
                            style="background:var(--sg-info-bg); color:var(--sg-navy-600)">{{ $subject?->code ?? '—' }}</span>
                        <span class="sg-badge" style="background:var(--sg-brass-100); color:var(--sg-brass-600)">
                            {{ $sy?->name ?? 'All terms' }}
                            @if($assignment->semester) · {{ $assignment->semester }} @endif
                        </span>
                        <span
                            class="sg-badge {{ $assignment->status === 'active' ? 'sg-badge-success' : 'sg-badge-info' }}">
                            {{ ucfirst($assignment->status ?? 'active') }}
                        </span>
                        @if($assignment->class_code)
                            <span class="sg-badge sg-badge-info">{{ $assignment->class_code }}</span>
                        @endif
                    </div>
                    <h2 class="font-display text-2xl font-bold" style="color:var(--sg-text)">
                        {{ $subject?->name ?? 'Subject' }}
                    </h2>
                    @if($subject?->description)
                        <p class="text-sm mt-1 max-w-2xl" style="color:var(--sg-text-muted)">
                            {{ $subject->description }}
                        </p>
                    @endif

                    <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm"
                        style="color:var(--sg-text-muted)">
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                            {{ $section?->name ?? '—' }}
                            @if($section?->block) · Block {{ $section->block }} @endif
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                            {{ $section?->short_course_name ?? '—' }}
                            @if($section?->year_level) · {{ $section->year_level }} @endif
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="book-open" class="w-4 h-4"></i>
                            {{ $subject?->units ?? '—' }}
                            {{ \Illuminate\Support\Str::plural('unit', (int) ($subject?->units ?? 0)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 no-print">
            @include('components.stat-card', [
                'label' => 'Enrolled Students',
                'value' => $totalStudents,
                'icon' => 'users',
                'color' => 'navy',
            ])
            @include('components.stat-card', [
                'label' => 'Grades Recorded',
                'value' => $gradedCount,
                'icon' => 'file-check-2',
                'color' => 'success',
            ])
            @include('components.stat-card', [
                'label' => 'Submission Status',
                'value' => $totalStudents > 0
                    ? $submittedCount . ' / ' . $totalStudents
                    : '0',
                'icon' => 'send',
                'color' => $submittedCount === $totalStudents && $totalStudents > 0 ? 'success' : 'warning',
            ])
            @include('components.stat-card', [
                'label' => 'Class Average',
                'value' => $classAverage !== null
                    ? number_format((float) $classAverage, 2) . '  ·  ' . number_format(\App\Support\GradingScale::equivalent((float) $classAverage), 1)
                    : '—',
                'icon' => 'chart-line',
                'color' => 'brass',
            ])
        </div>

        {{-- Grade distribution + summary --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 no-print">

            {{-- Distribution --}}
            <div class="sg-card p-5 lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-display font-semibold" style="color:var(--sg-text)">Grade Distribution</h3>
                        <p class="text-xs" style="color:var(--sg-text-muted)">
                            {{ $totalDist }} {{ \Illuminate\Support\Str::plural('grade', $totalDist) }} recorded
                        </p>
                    </div>
                </div>
                @if ($totalDist > 0)
                    <div class="space-y-3">
                        @foreach ($distribution as $band => $count)
                            @php
                                $pct = $totalDist > 0 ? round(($count / $totalDist) * 100) : 0;
                                $style = $gradeMap[$band] ?? $gradeMap['Below 75'];
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-medium" style="color:var(--sg-text)">{{ $band }}</span>
                                    <span style="color:var(--sg-text-muted)">
                                        {{ $count }} · {{ $pct }}%
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

            {{-- Class info --}}
            <div class="sg-card p-5">
                <h3 class="font-display font-semibold mb-3" style="color:var(--sg-text)">Class Details</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt style="color:var(--sg-text-muted)">Section</dt>
                        <dd class="text-right" style="color:var(--sg-text)">
                            {{ $section?->name ?? '—' }}
                            @if($section?->block)<br><span style="color:var(--sg-text-muted)">Block
                            {{ $section->block }}</span>@endif
                        </dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt style="color:var(--sg-text-muted)">Course</dt>
                        <dd class="text-right" style="color:var(--sg-text)">{{ $section?->short_course_name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt style="color:var(--sg-text-muted)">Year level</dt>
                        <dd class="text-right" style="color:var(--sg-text)">{{ $section?->year_level ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt style="color:var(--sg-text-muted)">Adviser</dt>
                        <dd class="text-right" style="color:var(--sg-text)">{{ $section?->adviser?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3 pt-3 border-t" style="border-color:var(--sg-border)">
                        <dt style="color:var(--sg-text-muted)">Teacher</dt>
                        <dd class="text-right" style="color:var(--sg-text)">{{ $teacher?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt style="color:var(--sg-text-muted)">Class code</dt>
                        <dd class="text-right" style="color:var(--sg-text)">{{ $assignment->class_code ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt style="color:var(--sg-text-muted)">School year</dt>
                        <dd class="text-right" style="color:var(--sg-text)">
                            {{ $sy?->name ?? '—' }}
                            @if($assignment->semester)<br><span
                            style="color:var(--sg-text-muted)">{{ $assignment->semester }}</span>@endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Grade matrix --}}
        <div class="sg-card p-5 print-area">
            <div class="flex items-center justify-between mb-4 no-print-flex">
                <div>
                    <h3 class="font-display font-semibold" style="color:var(--sg-text)">Student Grades</h3>
                    <p class="text-xs" style="color:var(--sg-text-muted)">
                        {{ $totalStudents }} {{ \Illuminate\Support\Str::plural('student', $totalStudents) }} enrolled in
                        this class offering
                    </p>
                </div>
            </div>

            {{-- Print-only institutional header --}}
            <div class="print-only mb-5">
                <div class="text-center">
                    <p class="text-xs uppercase tracking-[0.22em]" style="color:var(--sg-text-muted)">
                        Christ the King College · Gingoog City
                    </p>
                    <h2 class="font-display text-2xl font-bold mt-1" style="color:var(--sg-text)">
                        {{ $subject?->name ?? 'Subject' }}
                        <span style="color:var(--sg-text-muted)">—</span>
                        {{ $subject?->code ?? '' }}
                    </h2>
                    <p class="text-sm mt-1" style="color:var(--sg-text)">
                        {{ $section?->short_course_name ?? '—' }}
                        @if($section?->year_level) · {{ $section->year_level }} @endif
                        @if($section?->block) · Block {{ $section->block }} @endif
                        <span style="color:var(--sg-text-muted)">|</span>
                        {{ $assignment->semester ?? '—' }},
                        S.Y. {{ $sy?->name ?? '—' }}
                    </p>
                    <p class="text-xs mt-1" style="color:var(--sg-text-muted)">
                        Units: {{ $subject?->units ?? '—' }}
                        @if($teacher) · Teacher: {{ $teacher->name }} @endif
                    </p>
                </div>
                <hr class="my-4" style="border-color:var(--sg-border)">
            </div>

            <div class="overflow-x-auto">
                <table class="sg-table">
                    <thead>
                        <tr>
                            <th class="w-10">#</th>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th class="text-center">Course · Year · Block</th>
                            <th class="text-center">Units</th>
                            <th class="text-center">Prelim (Eq.)</th>
                            <th class="text-center">Midterm (Eq.)</th>
                            <th class="text-center">Final (Eq.)</th>
                            <th>Remarks</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gradeRows as $i => $row)
                            @php
                                $prelim = $row['prelim'] !== null ? (float) $row['prelim'] : null;
                                $midterm = $row['midterm'] !== null ? (float) $row['midterm'] : null;
                                $final = $row['final_grade'] !== null ? (float) $row['final_grade'] : null;
                                $prelimEq = $prelim !== null ? \App\Support\GradingScale::equivalent($prelim) : null;
                                $midtermEq = $midterm !== null ? \App\Support\GradingScale::equivalent($midterm) : null;
                                $finalEq = $final !== null ? \App\Support\GradingScale::equivalent($final) : null;
                                $remarks = $row['remarks'] ?? null;
                                $status = $row['status'] ?? null;
                                $remarkColor = match (strtolower((string) $remarks)) {
                                    'passed' => 'sg-badge-success',
                                    'failed' => 'sg-badge-danger',
                                    'inc' => 'sg-badge-warning',
                                    'dropped' => 'sg-badge-info',
                                    default => null,
                                };
                                $statusColor = match ($status) {
                                    'submitted' => 'sg-badge-success',
                                    'draft' => 'sg-badge-warning',
                                    default => null,
                                };
                            @endphp
                            <tr>
                                <td style="color:var(--sg-text-muted)">{{ $i + 1 }}</td>
                                <td style="color:var(--sg-text)">{{ $row['student_name'] }}</td>
                                <td style="color:var(--sg-text-muted)">{{ $row['student_no'] }}</td>
                                <td class="text-center" style="color:var(--sg-text-muted)">
                                    {{ trim(($row['course'] ?? '') . ' · ' . ($row['year_level'] ?? '') . ($row['block'] ? ' · Block ' . $row['block'] : ''), ' ·') ?: '—' }}
                                </td>
                                <td class="text-center" style="color:var(--sg-text)">
                                    {{ $subject?->units ?? '—' }}
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
                                <td>
                                    @if ($remarkColor)
                                        <span class="sg-badge {{ $remarkColor }}">{{ ucfirst((string) $remarks) }}</span>
                                    @else
                                        <span style="color:var(--sg-text-muted)">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($statusColor)
                                        <span class="sg-badge {{ $statusColor }}">{{ ucfirst($status) }}</span>
                                    @else
                                        <span style="color:var(--sg-text-muted)">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-10" style="color:var(--sg-text-muted)">
                                    No enrolled students in this class offering yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Print-only registrar signature --}}
            <div class="print-only mt-10 grid grid-cols-2 gap-10 text-sm">
                <div class="text-center">
                    <div class="flex h-[60px] items-end justify-center">
                        @if ($signatureUrl)
                            <img src="{{ $signatureUrl }}" alt="Registrar signature"
                                style="max-width: 220px; max-height: 58px; object-fit: contain;">
                        @endif
                    </div>
                    <div class="pt-1">
                        <p class="mx-auto w-[220px] border-b border-black pb-1 text-xs font-semibold" style="color: #000;">
                            {{ $signatoryName }}
                        </p>
                        <p class="text-[10px] mt-0.5" style="color: #555;">College Registrar</p>
                        <p class="text-[10px]" style="color: #555;">Christ the King College</p>
                    </div>
                </div>
                <div class="text-center">
                    <div style="height: 60px;"></div>
                    <div class="border-t pt-1" style="border-color: #000;">
                        <p class="font-semibold" style="color: #000;">_________________________</p>
                        <p class="text-xs mt-0.5 font-semibold" style="color: #000;">{{ $teacher?->name ?? 'Class Teacher' }}</p>
                        <p class="text-[10px]" style="color: #555;">Class Teacher</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection