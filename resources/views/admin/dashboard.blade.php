@extends('layouts.app', ['role' => 'admin', 'active' => 'dashboard', 'title' => 'Dashboard'])

@section('content')
  <div class="space-y-6">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      @include('components.stat-card', ['label' => 'Registered Students', 'value' => $studentCount, 'icon' => 'users', 'color' => 'navy'])
      @include('components.stat-card', ['label' => 'Registered Teachers', 'value' => $teacherCount, 'icon' => 'graduation-cap', 'color' => 'brass'])
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

      {{-- Grade distribution chart --}}
      <div class="sg-card p-5 xl:col-span-2">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="font-display font-semibold" style="color:var(--sg-text)">Grade Distribution</h3>
            <p class="text-xs" style="color:var(--sg-text-muted)">School-wide, current semester</p>
          </div>
          <span class="sg-badge sg-badge-info">All Sections</span>
        </div>
        <div class="py-10 text-center text-sm" style="color:var(--sg-text-muted)">No grade data available yet.</div>
      </div>

      {{-- Student performance overview --}}
      <div class="sg-card p-5">
        <h3 class="font-display font-semibold mb-1" style="color:var(--sg-text)">Performance Overview</h3>
        <p class="text-xs mb-4" style="color:var(--sg-text-muted)">Average GWA by year level</p>
        <div class="py-10 text-center text-sm" style="color:var(--sg-text-muted)">Performance data will appear after
          grades are recorded.</div>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

      {{-- Recent grade submissions --}}
      <div class="sg-card p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-display font-semibold" style="color:var(--sg-text)">Recent Grade Submissions</h3>
          <a href="{{ route('admin.reports') }}" class="text-xs font-medium" style="color:var(--sg-navy-500)">View
            all</a>
        </div>
        <p class="py-10 text-center text-sm" style="color:var(--sg-text-muted)">No grade submissions yet.</p>
      </div>

      {{-- Recently added students --}}
      <div class="sg-card p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-display font-semibold" style="color:var(--sg-text)">Recently Added Students</h3>
          <a href="{{ route('admin.students') }}" class="text-xs font-medium" style="color:var(--sg-navy-500)">View
            all</a>
        </div>
        @forelse ($recentStudents as $student)
          <div class="flex items-center justify-between gap-3 py-2 border-b last:border-0"
            style="border-color:var(--sg-border)">
            <div class="min-w-0">
              <p class="text-sm font-medium truncate" style="color:var(--sg-text)">{{ $student->name }}</p>
              <p class="text-xs truncate" style="color:var(--sg-text-muted)">{{ $student->student_id }}</p>
            </div>
            <span class="text-xs" style="color:var(--sg-text-muted)">{{ $student->created_at->format('M d, Y') }}</span>
          </div>
        @empty
          <p class="py-10 text-center text-sm" style="color:var(--sg-text-muted)">No student accounts yet.</p>
        @endforelse
      </div>
    </div>

  </div>
@endsection