@extends('layouts.app', ['role' => 'student', 'active' => 'subjects', 'title' => 'My Subjects'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Enrolled Subjects</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">
      {{ $schoolYear?->semester ?? 'First Semester' }}, {{ $schoolYear?->name ?? '2026–2027' }} · {{ $totalUnits }} total units
    </p>
  </div>

  @forelse ($subjects as $subject)
  @php $offering = $subjectOfferings[$subject->id] ?? null; @endphp
  <div class="sg-card p-5">
    <div class="flex items-start justify-between gap-4">
      <div class="min-w-0">
        <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">{{ $subject->code }}</h3>
        <p class="text-sm" style="color:var(--sg-text-muted)">{{ $subject->name }}</p>
        <p class="text-xs mt-1" style="color:var(--sg-text-muted)">{{ $subject->units }} unit(s)</p>
        @if($offering)
          <div class="mt-2 flex items-center gap-2 flex-wrap">
            <span class="text-xs" style="color:var(--sg-text-muted)">
              {{ $offering->section?->name ?? '' }} · {{ $offering->section?->year_level ?? '' }}
            </span>
          </div>
        @endif
      </div>
      <span class="sg-badge sg-badge-info">{{ $subject->units }} units</span>
    </div>
  </div>
  @empty
  <div class="sg-card p-8 text-center">
    <i data-lucide="book-open" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
    <p class="text-sm" style="color:var(--sg-text-muted)">You are not enrolled in any subjects for this semester.</p>
  </div>
  @endforelse
</div>
@endsection
