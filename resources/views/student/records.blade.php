@extends('layouts.app', ['role' => 'student', 'active' => 'records', 'title' => 'Academic Records'])

@section('content')
  <div class="space-y-5">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Academic Records</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">Completed semesters and grade history</p>
    </div>

    @forelse ($records as $record)
      <div class="sg-card p-5">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="font-display font-semibold" style="color:var(--sg-text)">{{ $record['term'] }}</h3>
          </div>
          <div class="text-right">
            <p class="text-xs" style="color:var(--sg-text-muted)">Semester GWA (eq.)</p>
            <p class="font-display font-bold text-xl" style="color:var(--sg-brass-500)">
              @php
                $recGwa = is_numeric($record['gwa']) ? (float) $record['gwa'] : null;
                $recEq  = $recGwa !== null ? \App\Support\GradingScale::equivalent($recGwa) : null;
              @endphp
              {{ $recEq !== null ? number_format($recEq, 1) : '—' }}
            </p>
          </div>
        </div>
        <div class="overflow-x-auto no-print">
          <table class="sg-table">
            <thead>
              <tr>
                <th>Subject</th>
                <th>Units</th>
                <th class="text-center">Prelim (Eq.)</th>
                <th class="text-center">Midterm (Eq.)</th>
                <th class="text-center">Final (Eq.)</th>
                <th class="text-center">Equivalent</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($record['grades'] as $grade)
                @php
                  $prelimEq  = $grade->prelim  !== null ? \App\Support\GradingScale::equivalent((float) $grade->prelim)  : null;
                  $midtermEq = $grade->midterm !== null ? \App\Support\GradingScale::equivalent((float) $grade->midterm) : null;
                  $finalEq   = $grade->final   !== null ? \App\Support\GradingScale::equivalent((float) $grade->final)   : null;
                @endphp
                <tr>
                  <td class="font-medium" style="color:var(--sg-text)">{{ $grade->subject->name ?? '—' }} <span
                      class="text-xs" style="color:var(--sg-text-muted)">({{ $grade->subject->code ?? '' }})</span></td>
                  <td style="color:var(--sg-text-muted)">{{ $grade->subject->units ?? '—' }}</td>
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
                  <td colspan="7" class="text-center py-6" style="color:var(--sg-text-muted)">No grades recorded.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    @empty
      <div class="sg-card p-8 text-center">
        <i data-lucide="archive" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
        <p class="text-sm" style="color:var(--sg-text-muted)">No academic records available yet.</p>
      </div>
    @endforelse
  </div>
@endsection