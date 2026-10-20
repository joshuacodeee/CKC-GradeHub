@extends('layouts.app', ['role' => 'student', 'active' => 'grades', 'title' => 'My Grades'])

@php
  $user = auth()->user();
  $studentName = $user?->name ?? '—';
  $studentId = $user?->student_id ?? '—';
  $course = $user?->course ?? '—';
  $yearLevel = $user?->year_level ?? '—';
  $block = $user?->block;
  $sex = $user?->sex ?: '—';
  $reportName = $user?->last_name_first_middle ?: $studentName;
  $courseYearLvl = $user?->course_year_level ?: '—';
  $schoolYearName = $schoolYear?->name ?? date('Y') . '–' . (date('Y') + 1);
  $semester = $schoolYear?->semester ?? 'First Semester';
  $printedAt = now()->format('F d, Y');
  $studentInitials = collect(explode(' ', $studentName))->filter()->take(2)
    ->map(fn($p) => strtoupper(substr($p, 0, 1)))->join('');

  $gwaEquivalent = $gwa !== null ? \App\Support\GradingScale::equivalent((float) $gwa) : null;
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

      {{-- Print-only institutional header / report card masthead --}}
      <div class="print-only mb-6">
        <div class="flex items-start gap-5">
          {{-- Left: circular CKC monogram --}}
          <div class="flex-shrink-0">
            <svg width="84" height="84" viewBox="0 0 84 84" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <defs>
                <linearGradient id="rc-ring" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#0b1a33" />
                  <stop offset="100%" stop-color="#1d4d82" />
                </linearGradient>
              </defs>
              <circle cx="42" cy="42" r="40" fill="#ffffff" stroke="url(#rc-ring)" stroke-width="3" />
              <circle cx="42" cy="42" r="34" fill="none" stroke="#c8952f" stroke-width="1.2" />
              <text x="42" y="50" text-anchor="middle" font-family="Sora, Inter, sans-serif" font-weight="800"
                font-size="22" letter-spacing="1.5" fill="#0b1a33">CKC</text>
            </svg>
          </div>

          {{-- Middle: institutional title --}}
          <div class="flex-1 text-center pt-1">
            <p class="font-display font-extrabold tracking-wide text-lg leading-tight" style="color:var(--sg-navy-900)">
              CHRIST THE KING COLLEGE
            </p>
            <p class="text-sm mt-0.5" style="color:var(--sg-text)">
              9014 Gingoog City, Philippines
            </p>
            <p class="text-sm" style="color:var(--sg-text)">
              Higher Education Department
            </p>
            <p class="text-xs uppercase tracking-[0.32em] mt-2 font-semibold" style="color:var(--sg-brass-600)">
              Report Card
            </p>
            <p class="text-[10px] uppercase tracking-[0.24em] mt-1" style="color:var(--sg-text-muted)">
              Official · Read-only copy
            </p>
          </div>
        </div>

        <hr class="my-4" style="border-color:var(--sg-border)">

        {{-- Term + identification block --}}
        <div class="text-sm space-y-1.5">
          <div class="flex flex-wrap gap-x-8 gap-y-1">
            <div>
              <span style="color:var(--sg-text-muted)">Semester:</span>
              <span class="font-semibold ml-1" style="color:var(--sg-text)">{{ $semester }}</span>
            </div>
            <div>
              <span style="color:var(--sg-text-muted)">School Year:</span>
              <span class="font-semibold ml-1" style="color:var(--sg-text)">{{ $schoolYearName }}</span>
            </div>
          </div>
          <div>
            <span style="color:var(--sg-text-muted)">Name:</span>
            <span class="font-semibold ml-1" style="color:var(--sg-text)">{{ $reportName }}</span>
          </div>
          <div class="flex flex-wrap gap-x-8 gap-y-1">
            <div>
              <span style="color:var(--sg-text-muted)">ID No:</span>
              <span class="font-semibold ml-1" style="color:var(--sg-text)">{{ $studentId }}</span>
            </div>
            <div>
              <span style="color:var(--sg-text-muted)">Course Lvl:</span>
              <span class="font-semibold ml-1" style="color:var(--sg-text)">{{ $courseYearLvl }}</span>
            </div>
            <div>
              <span style="color:var(--sg-text-muted)">SEX:</span>
              <span class="font-semibold ml-1" style="color:var(--sg-text)">{{ $sex }}</span>
            </div>
          </div>
        </div>
        <hr class="my-4" style="border-color:var(--sg-border)">

        {{-- Report card subject table --}}
        <table class="w-full text-sm" style="border-collapse: collapse;">
          <thead>
            <tr style="background:#f3f7fc; border-bottom: 2px solid #1d4d82;">
              <th class="text-left py-2 px-2 font-semibold uppercase tracking-wide text-[11px]" style="color:#1d4d82;">
                Subject</th>
              <th class="text-center py-2 px-2 font-semibold uppercase tracking-wide text-[11px] w-20"
                style="color:#1d4d82;">Prelims</th>
              <th class="text-center py-2 px-2 font-semibold uppercase tracking-wide text-[11px] w-16"
                style="color:#1d4d82;">MT</th>
              <th class="text-center py-2 px-2 font-semibold uppercase tracking-wide text-[11px] w-20"
                style="color:#1d4d82;">Finals</th>
              <th class="text-center py-2 px-2 font-semibold uppercase tracking-wide text-[11px] w-20"
                style="color:#1d4d82;">Credits</th>
              <th class="text-left py-2 px-2 font-semibold uppercase tracking-wide text-[11px]" style="color:#1d4d82;">
                Professor</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($grades as $grade)
              @php
                $prelimEq = $grade->prelim !== null ? \App\Support\GradingScale::equivalent((float) $grade->prelim) : null;
                $midtermEq = $grade->midterm !== null ? \App\Support\GradingScale::equivalent((float) $grade->midterm) : null;
                $finalEq = $grade->final !== null ? \App\Support\GradingScale::equivalent((float) $grade->final) : null;
                $professor = $grade->teacherAssignment?->teacher?->name ?? '—';
              @endphp
              <tr style="border-bottom: 1px solid #e7eff8;">
                <td class="py-2 px-2 align-top" style="color:var(--sg-text)">
                  <div class="font-semibold">
                    {{ $grade->subject?->code ?? '—' }} — {{ $grade->subject?->name ?? '—' }}
                  </div>
                </td>
                <td class="text-center py-2 px-2 align-top font-semibold" style="color:var(--sg-navy-700)">
                  {{ $prelimEq !== null ? number_format($prelimEq, 1) : '—' }}
                </td>
                <td class="text-center py-2 px-2 align-top font-semibold" style="color:var(--sg-navy-700)">
                  {{ $midtermEq !== null ? number_format($midtermEq, 1) : '—' }}
                </td>
                <td class="text-center py-2 px-2 align-top font-semibold" style="color:var(--sg-navy-700)">
                  {{ $finalEq !== null ? number_format($finalEq, 1) : '—' }}
                </td>
                <td class="text-center py-2 px-2 align-top" style="color:var(--sg-text)">
                  {{ $grade->subject?->units ?? '—' }}
                </td>
                <td class="py-2 px-2 align-top text-xs" style="color:var(--sg-text)">
                  {{ $professor }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-6" style="color:var(--sg-text-muted)">
                  No grades recorded for this term.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        {{-- GPA line, below the subject table --}}
        <div class="mt-4 pt-3 flex flex-wrap items-baseline gap-x-6 gap-y-1 text-sm"
          style="border-top: 1px solid #e7eff8;">
          <div>
            <span style="color:var(--sg-text-muted)">GPA:</span>
            <span class="font-bold ml-1" style="color:var(--sg-navy-700)">
              {{ $gwaEquivalent !== null ? number_format($gwaEquivalent, 1) : '—' }}
            </span>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto no-print">
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
          <div class="absolute inset-0 flex items-center justify-center pointer-events-none" style="z-index: 0;">
            <div class="text-center" style="opacity: 0.08;">
              <div class="font-display font-extrabold tracking-widest"
                style="font-size: 180px; line-height: 1; color: var(--sg-navy-900);">
                CKC
              </div>
              <p class="font-display font-bold tracking-[0.3em] mt-2" style="font-size: 28px; color: var(--sg-navy-900);">
                CHRIST THE KING COLLEGE
              </p>
              <p class="text-xs tracking-[0.4em] mt-1" style="color: var(--sg-navy-700);">
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
                  <p class="text-[10px] mt-1" style="color: #555;">Date issued: {{ $printedAt }}</p>
                  <p class="text-[10px]" style="color: #555;">Reference: CKC-GH/{{ $schoolYearName }}/{{ $studentId }}</p>
                </div>
              </div>
              <div class="text-center">
                <div style="height: 78px;"></div>
                <div class="relative mx-auto h-[68px] w-full max-w-[260px]">
                  @if ($signatureUrl)
                    <img src="{{ $signatureUrl }}" alt="Official registrar signature"
                      class="absolute inset-x-0 bottom-0 z-10 mx-auto max-h-[68px] max-w-[190px] object-contain">
                  @endif
                  <p class="absolute inset-x-0 bottom-2 border-b border-black pb-1 text-xs font-bold" style="color: #000;">
                    {{ $signatoryName }}
                  </p>
                </div>
                <p class="text-[10px] font-semibold" style="color: #000;">College Registrar</p>
                <p class="text-[10px]" style="color: #555;">Christ the King College</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    @include('components.notification', [
      'type' => 'info',
      'title' => 'How your final grade is computed',
      'message' => 'Final Grade = (Prelim + Midterm + Final) / 3. A grade of 75 or higher is passing.',
    ])
  </div>
@endsection