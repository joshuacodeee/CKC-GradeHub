@extends('layouts.app', ['role' => 'teacher', 'active' => 'reports', 'title' => 'Reports'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Class Reports</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">
      {{ $schoolYear?->semester ?? 'First Semester' }}, {{ $schoolYear?->name ?? '2026–2027' }}
    </p>
  </div>

  @forelse ($submissions as $sub)
  <div class="sg-card p-5">
    <div class="flex items-start justify-between gap-4">
      <div class="min-w-0">
        <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">
          {{ $sub->subject->code ?? '—' }} — {{ $sub->subject->name ?? 'Unnamed' }}
        </h3>
        <p class="text-sm" style="color:var(--sg-text-muted)">
          {{ $sub->section->name ?? '—' }} · {{ $sub->section->course ?? '' }} {{ $sub->section->year_level ?? '' }}
        </p>
        <p class="text-xs mt-1" style="color:var(--sg-text-muted)">
          {{ $sub->submitted_count }}/{{ $sub->total_students }} students submitted
          @if($sub->avg_grade)
            · Average: {{ round($sub->avg_grade, 2) }}
          @endif
        </p>
      </div>
      <div class="flex items-center gap-2">
        @if($sub->status === 'Submitted')
          <span class="sg-badge sg-badge-success">{{ $sub->status }}</span>
        @elseif($sub->status === 'Partial')
          <span class="sg-badge sg-badge-warning">{{ $sub->status }}</span>
        @else
          <span class="sg-badge sg-badge-info">{{ $sub->status }}</span>
        @endif
        <button class="sg-btn sg-btn-ghost !p-2" data-toast="Opening detailed report for {{ $sub->subject->code ?? '' }} — {{ $sub->section->name ?? '' }} (demo only).">
          <i data-lucide="external-link" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>
  @empty
  <div class="sg-card p-8 text-center">
    <i data-lucide="file-bar-chart-2" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
    <p class="text-sm" style="color:var(--sg-text-muted)">No grade submissions for this semester yet.</p>
  </div>
  @endforelse
</div>
@endsection
