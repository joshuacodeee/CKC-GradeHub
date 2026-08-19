@extends('layouts.app', ['role' => 'teacher', 'active' => 'dashboard', 'title' => 'Dashboard'])

@section('content')
<div class="space-y-6">

  @include('components.notification', [
    'type' => 'warning', 'title' => 'Grade submission reminder',
    'message' => 'Final grades for Web Development — BSCS 1-A are due this Friday, August 14.',
  ])

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    @include('components.stat-card', ['label' => 'Assigned Sections', 'value' => '3', 'icon' => 'presentation', 'color' => 'navy'])
    @include('components.stat-card', ['label' => 'Total Students', 'value' => '125', 'icon' => 'users', 'color' => 'brass'])
    @include('components.stat-card', ['label' => 'Pending Grades', 'value' => '15', 'icon' => 'clock', 'color' => 'warning'])
  </div>

  <div>
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-display font-semibold" style="color:var(--sg-text)">My Classes</h3>
      <a href="{{ route('teacher.classes') }}" class="text-xs font-medium" style="color:var(--sg-navy-500)">View all classes</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="teacher-classes-cards"></div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="sg-card p-5">
      <h3 class="font-display font-semibold mb-4" style="color:var(--sg-text)">Grade Progress</h3>
      <div class="space-y-4" id="teacher-progress"></div>
    </div>
    <div class="sg-card p-5">
      <h3 class="font-display font-semibold mb-4" style="color:var(--sg-text)">Recent Activity</h3>
      <ul class="space-y-3" id="teacher-activity"></ul>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Mock data placeholder
</script>
@endpush
