@extends('layouts.app', ['role' => 'student', 'active' => 'grades', 'title' => 'My Grades'])

@php
    $user = auth()->user();
    $studentName = $user?->name ?? '—';
    $studentId   = $user?->student_id ?? '—';
    $course      = $user?->course ?? '—';
    $yearLevel   = $user?->year_level ?? '—';
    $block       = $user?->block;
    $schoolYearName = $schoolYear?->name ?? date('Y') . '–' . (date('Y') + 1);
    $semester    = $schoolYear?->semester ?? 'First Semester';
    $printedAt   = now()->format('F d, Y');
    $studentInitials = collect(explode(' ', $studentName))->filter()->take(2)
        ->map(fn ($p) => strtoupper(substr($p, 0, 1)))->join('');
@endphp

@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3 no-print-flex">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">My Grades</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">
        {{ $semester }}, {{ $schoolYearName }}
      </p>
    </div>
    <button data-print class="sg-btn sg-btn-outline"><i data-lucide="printer" class="w-4 h-4"></i> Print</button>
  </div>

  <div class="sg-card p-5 print-area">

    {{-- Print-only institutional header --}}
    <div class="print-only mb-6">
      <div class="text-center">
        <p class="text-[10px] uppercase tracking-[0.28em]" style="color:var(--sg-text-muted)">
          Christ the King College · Gingoog City
        </p>
        <h1 class="font-display text-2xl font-bold mt-1" style="color:var(--sg-text)">
          Student Grade Report
        </h1>
        <p class="text-sm mt-1" style="color:var(--sg-text-muted)">
          {{ $semester }} · School Year {{ $schoolYearName }}
        </p>
      </div>
      <hr class="my-4" style="border-color:var(--sg-border)">

      <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-1.5 text-sm">
        <div>
          <span style="color:var(--sg-text-muted)">Name:</span>
          <span class="font-semibold ml-1" style="color:var(--sg-text)">{{ $studentName }}</span>
        </div>
        <div>
          <span style="color:var(--sg-text-muted)">Student ID:</span>
          <span class="font-semibold ml-1" style="color:var(--sg-text)">{{ $studentId }}</span>
        </div>
        <div>
          <span style="color:var(--sg-text-muted)">Course · Year · Block:</span>
          <span class="font-semibold ml-1" style="color:var(--sg-text)">
            {{ $course }} · {{ $yearLevel }}{{ $block ? ' · Block ' . $block : '' }}
          </span>
        </div>
      </div>
      <hr class="my-4" style="border-color:var(--sg-border)">
    </div>

    <div class="overflow-x-auto">
      <table class="sg-table">
        <thead>
          <tr>
            <th>Subject</th>
            <th class="text-center">Units</th>
            <th class="text-center">Prelim</th>
            <th class="text-center">Midterm</th>
            <th class="text-center">Final</th>
            <th class="text-center">Final Grade</th>
            <th class="text-center">Equivalent</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($grades as $grade)
            <tr>
              <td class="font-medium" style="color:var(--sg-text)">
                {{ $grade->subject->name ?? '—' }}
                <span class="text-xs" style="color:var(--sg-text-muted)">({{ $grade->subject->code ?? '' }})</span>
              </td>
              <td class="text-center" style="color:var(--sg-text)">{{ $grade->subject->units ?? '—' }}</td>
              <td class="text-center" style="color:var(--sg-text)">
                {{ $grade->prelim !== null ? number_format((float) $grade->prelim, 2) : '—' }}
              </td>
              <td class="text-center" style="color:var(--sg-text)">
                {{ $grade->midterm !== null ? number_format((float) $grade->midterm, 2) : '—' }}
              </td>
              <td class="text-center" style="color:var(--sg-text)">
                {{ $grade->final !== null ? number_format((float) $grade->final, 2) : '—' }}
              </td>
              <td class="text-center font-semibold" style="color:var(--sg-navy-700)">
                {{ $grade->final_grade !== null ? number_format((float) $grade->final_grade, 2) : '—' }}
              </td>
              <td class="text-center font-semibold" style="color:var(--sg-navy-700)">
                {{ $grade->grade_equivalent !== null ? number_format($grade->grade_equivalent, 1) : '—' }}
              </td>
              <td>
                @if($grade->remarks === 'Passed')
                  <span class="sg-badge sg-badge-success">{{ $grade->remarks }}</span>
                @elseif($grade->remarks === 'Failed')
                  <span class="sg-badge sg-badge-danger">{{ $grade->remarks }}</span>
                @else
                  <span class="sg-badge sg-badge-info">Pending</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-10" style="color:var(--sg-text-muted)">No grades recorded yet.</td>
            </tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr>
            <td class="font-semibold" colspan="4" style="color:var(--sg-text)">General Weighted Average</td>
            <td class="text-center font-bold" id="gwa-cell" style="color:var(--sg-navy-700)">
                {{ $gwa !== null ? number_format((float) $gwa, 2) : '—' }}
            </td>
            <td class="text-center font-bold" style="color:var(--sg-navy-700)">
                @php $gwaEq = $gwa !== null ? \App\Support\GradingScale::equivalent((float) $gwa) : null; @endphp
                {{ $gwaEq !== null ? number_format($gwaEq, 1) : '—' }}
            </td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>

    {{-- Print-only: large low-opacity school logo on the back (bottom of page) + registrar signature --}}
    <div class="print-only mt-12">
      <div class="relative" style="min-height: 5in;">
        {{-- Big low-opacity school logo (printed behind content) --}}
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none"
          style="z-index: 0;">
          <div class="text-center" style="opacity: 0.08;">
            <div class="font-display font-extrabold tracking-widest"
              style="font-size: 180px; line-height: 1; color: var(--sg-navy-900);">
              CKC
            </div>
            <p class="font-display font-bold tracking-[0.3em] mt-2"
              style="font-size: 28px; color: var(--sg-navy-900);">
              CHRIST THE KING COLLEGE
            </p>
            <p class="text-xs tracking-[0.4em] mt-1"
              style="color: var(--sg-navy-700);">
              GINGOOG CITY · EST.
            </p>
          </div>
        </div>

        {{-- Foreground: registrar signature line --}}
        <div class="relative" style="z-index: 1; padding-top: 3in;">
          <div class="grid grid-cols-2 gap-10 text-sm">
            <div class="text-center">
              <div style="height: 60px;"></div>
              <div class="border-t pt-1" style="border-color: #000;">
                <p class="font-semibold" style="color: #000;">_________________________</p>
                <p class="text-xs mt-0.5" style="color: #000;">School Registrar</p>
                <p class="text-[10px]" style="color: #555;">Christ the King College</p>
              </div>
            </div>
            <div class="text-center">
              <div style="height: 60px;"></div>
              <div class="border-t pt-1" style="border-color: #000;">
                <p class="text-[10px] mt-1" style="color: #555;">Date issued: {{ $printedAt }}</p>
                <p class="text-[10px]" style="color: #555;">Reference: CKC-GH/{{ $schoolYearName }}/{{ $studentId }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  @include('components.notification', [
    'type' => 'info', 'title' => 'How your final grade is computed',
    'message' => 'Final Grade = (Prelim + Midterm + Final) / 3. A grade of 75 or higher is passing.',
  ])
</div>
@endsection
