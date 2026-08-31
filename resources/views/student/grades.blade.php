@extends('layouts.app', ['role' => 'student', 'active' => 'grades', 'title' => 'My Grades'])

@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">My Grades</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">
        {{ $schoolYear?->semester ?? 'First Semester' }}, {{ $schoolYear?->name ?? '2026–2027' }}
      </p>
    </div>
    <button data-print class="sg-btn sg-btn-outline"><i data-lucide="printer" class="w-4 h-4"></i> Print</button>
  </div>

  <div class="sg-card p-5 print-area">
    <div class="overflow-x-auto">
      <table class="sg-table">
        <thead>
          <tr>
            <th>Subject</th>
            <th>Units</th>
            <th class="text-center">Prelim</th>
            <th class="text-center">Midterm</th>
            <th class="text-center">Final</th>
            <th class="text-center">Final Grade</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($grades as $grade)
            <tr>
              <td class="font-medium" style="color:var(--sg-text)">{{ $grade->subject->name ?? '—' }} <span class="text-xs" style="color:var(--sg-text-muted)">({{ $grade->subject->code ?? '' }})</span></td>
              <td style="color:var(--sg-text-muted)">{{ $grade->subject->units ?? '—' }}</td>
              <td class="text-center" style="color:var(--sg-text)">{{ $grade->prelim ?? '—' }}</td>
              <td class="text-center" style="color:var(--sg-text)">{{ $grade->midterm ?? '—' }}</td>
              <td class="text-center" style="color:var(--sg-text)">{{ $grade->final ?? '—' }}</td>
              <td class="text-center font-medium" style="color:var(--sg-text)">{{ $grade->final_grade ?? '—' }}</td>
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
              <td colspan="7" class="text-center py-10" style="color:var(--sg-text-muted)">No grades recorded yet.</td>
            </tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr>
            <td class="font-semibold" colspan="5" style="color:var(--sg-text)">General Weighted Average</td>
            <td class="text-center font-bold" id="gwa-cell" style="color:var(--sg-text)">{{ $gwa ?? '—' }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  @include('components.notification', [
    'type' => 'info', 'title' => 'How your final grade is computed',
    'message' => 'Final Grade = (Prelim + Midterm + Final) / 3. A grade of 75 or higher is passing.',
  ])
</div>
@endsection
