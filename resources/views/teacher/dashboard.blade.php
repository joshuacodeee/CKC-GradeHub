@extends('layouts.app', ['role' => 'teacher', 'active' => 'dashboard', 'title' => 'Dashboard'])

@section('content')
<div class="space-y-6">

  @if ($pendingGrades > 0)
    @include('components.notification', [
      'type' => 'warning',
      'title' => 'Grade submission reminder',
      'message' => "You have {$pendingGrades} grade record(s) pending submission. <a href=\"" . route('teacher.grades') . "\" class=\"font-medium underline\">Review now</a>.",
    ])
  @endif

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    @include('components.stat-card', ['label' => 'Assigned Classes', 'value' => $assignedClasses->count(), 'icon' => 'presentation', 'color' => 'navy'])
    @include('components.stat-card', ['label' => 'Total Students', 'value' => $totalStudents, 'icon' => 'users', 'color' => 'brass'])
    @include('components.stat-card', ['label' => 'Pending Grades', 'value' => $pendingGrades, 'icon' => 'clock', 'color' => 'warning'])
  </div>

  {{-- My Classes --}}
  <div>
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-display font-semibold" style="color:var(--sg-text)">My Classes</h3>
      <a href="{{ route('teacher.classes') }}" class="text-xs font-medium" style="color:var(--sg-navy-500)">View all classes</a>
    </div>
    @forelse ($assignedClasses as $class)
    <div class="sg-card p-4 mb-2 last:mb-0">
      <div class="flex items-center justify-between">
        <div class="min-w-0">
          <p class="font-medium" style="color:var(--sg-text)">{{ $class->subject->code ?? '—' }} — {{ $class->subject->name ?? 'Unnamed Subject' }}</p>
          <p class="text-sm" style="color:var(--sg-text-muted)">{{ $class->section->name ?? '—' }} · {{ $class->section->course ?? '' }} {{ $class->section->year_level ?? '' }}</p>
        </div>
        <a href="{{ route('teacher.grades.enter', $class) }}" class="sg-btn sg-btn-outline !py-1.5 !text-xs">
          <i data-lucide="square-pen" class="w-4 h-4"></i> Enter Grades
        </a>
      </div>
    </div>
    @empty
    <div class="sg-card p-8 text-center">
      <i data-lucide="presentation" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
      <p class="text-sm" style="color:var(--sg-text-muted)">No classes assigned to you for this semester.</p>
    </div>
    @endforelse
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="sg-card p-5">
      <h3 class="font-display font-semibold mb-4" style="color:var(--sg-text)">Grade Progress</h3>
      @forelse ($assignedClasses as $class)
        @php
          $enrollmentIds = \App\Models\Enrollment::where('section_id', $class->section_id)->pluck('id');
          $submitted = \App\Models\Grade::where('teacher_id', auth()->id())
              ->where('subject_id', $class->subject_id)
              ->whereIn('enrollment_id', $enrollmentIds)
              ->where('status', 'submitted')
              ->count();
          $total = $enrollmentIds->count();
          $pct = $total > 0 ? round(($submitted / $total) * 100) : 0;
        @endphp
        <div class="mb-3 last:mb-0">
          <div class="flex justify-between text-sm mb-1">
            <span style="color:var(--sg-text-muted)">{{ $class->subject->code ?? '—' }} — {{ $class->section->name ?? '—' }}</span>
            <span style="color:var(--sg-text)">{{ $submitted }}/{{ $total }}</span>
          </div>
          <div class="w-full bg-navy-100 rounded-full h-2" style="background:var(--sg-navy-50); border-radius:999px; overflow:hidden;">
            <div class="h-2 rounded-full transition-all" style="width:{{ $pct }}%; background:{{ $pct === 100 ? 'var(--sg-success)' : 'var(--sg-warning)' }};"></div>
          </div>
        </div>
      @empty
        <p class="text-sm" style="color:var(--sg-text-muted)">No grade data yet.</p>
      @endforelse
    </div>

    <div class="sg-card p-5">
      <h3 class="font-display font-semibold mb-4" style="color:var(--sg-text)">Recent Activity</h3>
      <ul class="space-y-3">
        @forelse ($assignedClasses as $class)
          <li class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
              style="background:var(--sg-info-bg)"><i data-lucide="file-check-2" class="w-4 h-4"
                style="color:var(--sg-navy-600)"></i></div>
            <div class="min-w-0">
              <p class="text-sm" style="color:var(--sg-text)">Grading for {{ $class->subject->code ?? '—' }} — {{ $class->section->name ?? '—' }}</p>
              <p class="text-xs" style="color:var(--sg-text-muted)">{{ $class->section->course ?? '' }} · {{ $schoolYear?->name ?? '' }}</p>
            </div>
          </li>
        @empty
          <li class="text-sm" style="color:var(--sg-text-muted)">No recent activity.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@endsection
