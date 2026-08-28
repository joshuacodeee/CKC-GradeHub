@extends('layouts.app', ['role' => 'teacher', 'active' => 'grades', 'title' => 'Enter Grades'])

@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Enter Grades</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">
        {{ $schoolYear?->semester ?? 'First Semester' }}, {{ $schoolYear?->name ?? '2026–2027' }}
      </p>
    </div>
    <a href="{{ route('teacher.grades') }}" class="sg-btn sg-btn-outline !py-1.5 !text-xs">
      <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Classes
    </a>
  </div>

  @forelse ($classes as $class)
  <div class="sg-card p-5">
    <div class="flex items-start justify-between gap-4">
      <div class="min-w-0">
        <h3 class="font-display font-semibold text-lg" style="color:var(--sg-text)">
          {{ $class->subject->code ?? '—' }} — {{ $class->subject->name ?? 'Unnamed' }}
        </h3>
        <p class="text-sm" style="color:var(--sg-text-muted)">
          {{ $class->section->name ?? '—' }} · {{ $class->section->course ?? '' }} {{ $class->section->year_level ?? '' }}
        </p>
      </div>
      <div class="flex gap-2">
        @php
          $enrollmentIds = \App\Models\Enrollment::where('section_id', $class->section_id)->pluck('id');
          $submitted = \App\Models\Grade::where('teacher_id', auth()->id())
              ->where('subject_id', $class->subject_id)
              ->whereIn('enrollment_id', $enrollmentIds)
              ->where('status', 'submitted')
              ->count();
          $total = $enrollmentIds->count();
        @endphp
        <span class="sg-badge sg-badge-info">{{ $submitted }}/{{ $total }} submitted</span>
        <a href="{{ route('teacher.grades.enter', $class) }}" class="sg-btn sg-btn-primary !py-1.5 !text-xs">
          <i data-lucide="square-pen" class="w-4 h-4"></i> Enter Grades
        </a>
      </div>
    </div>
  </div>
  @empty
  <div class="sg-card p-8 text-center">
    <i data-lucide="square-pen" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
    <p class="text-sm" style="color:var(--sg-text-muted)">No classes assigned to you for this semester.</p>
  </div>
  @endforelse
</div>
@endsection
