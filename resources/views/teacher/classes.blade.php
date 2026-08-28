@extends('layouts.app', ['role' => 'teacher', 'active' => 'classes', 'title' => 'My Classes'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">My Classes</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">
      {{ $schoolYear?->semester ?? 'First Semester' }}, {{ $schoolYear?->name ?? '2026–2027' }}
    </p>
  </div>

  @forelse ($classes as $class)
  <div class="sg-card p-5">
    <div class="flex items-start justify-between gap-4">
      <div class="min-w-0">
        <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">{{ $class->subject->name ?? 'Unnamed Subject' }}</h3>
        <p class="text-sm" style="color:var(--sg-text-muted)">
          {{ $class->subject->code ?? '—' }} · {{ $class->section->name ?? '—' }}
        </p>
        <p class="text-xs mt-1" style="color:var(--sg-text-muted)">
          {{ $class->section->course ?? '' }} · {{ $class->section->year_level ?? '' }}
          · {{ $class->section->enrollments()->where('status', 'enrolled')->count() }} students
        </p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('teacher.grades.enter', $class) }}" class="sg-btn sg-btn-primary !py-1.5 !text-xs">
          <i data-lucide="square-pen" class="w-4 h-4"></i> Enter Grades
        </a>
      </div>
    </div>
  </div>
  @empty
  <div class="sg-card p-8 text-center">
    <i data-lucide="presentation" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
    <p class="text-sm" style="color:var(--sg-text-muted)">No classes assigned to you for this semester.</p>
  </div>
  @endforelse
</div>
@endsection
