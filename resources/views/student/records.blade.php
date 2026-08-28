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
        <p class="text-xs" style="color:var(--sg-text-muted)">Semester GWA</p>
        <p class="font-display font-bold text-xl" style="color:var(--sg-brass-500)">{{ $record['gwa'] ?? '—' }}</p>
      </div>
    </div>
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
          @forelse ($record['grades'] as $grade)
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
