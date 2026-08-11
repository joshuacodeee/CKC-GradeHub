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
  document.getElementById('teacher-classes-cards').innerHTML = teacherClasses.map(c => `
    <div class="sg-card p-5">
      <div class="flex items-center justify-between mb-2">
        <span class="sg-badge sg-badge-info">${c.subjectCode}</span>
        ${c.pending > 0 ? `<span class="sg-badge sg-badge-warning">${c.pending} pending</span>` : `<span class="sg-badge sg-badge-success">Complete</span>`}
      </div>
      <h4 class="font-display font-semibold" style="color:var(--sg-text)">${c.subject}</h4>
      <p class="text-sm mb-3" style="color:var(--sg-text-muted)">${c.section} · ${c.schedule}</p>
      <div class="flex items-center justify-between text-sm">
        <span class="flex items-center gap-1.5" style="color:var(--sg-text-muted)"><i data-lucide="users" class="w-4 h-4"></i> ${c.students} Students</span>
        <a href="/teacher/grades?subject=${c.subjectCode}&section=${encodeURIComponent(c.section)}" class="font-medium" style="color:var(--sg-navy-500)">Enter grades →</a>
      </div>
    </div>
  `).join('');

  document.getElementById('teacher-progress').innerHTML = teacherClasses.map(c => {
    const pct = Math.round((c.submitted / c.students) * 100);
    return `
    <div>
      <div class="flex justify-between text-sm mb-1">
        <span style="color:var(--sg-text)">${c.subject} — ${c.section}</span>
        <span class="font-semibold" style="color:var(--sg-text)">${c.submitted}/${c.students}</span>
      </div>
      <div class="h-2 rounded-full" style="background:var(--sg-navy-50)">
        <div class="h-2 rounded-full" style="width:${pct}%; background:${pct === 100 ? 'var(--sg-success)' : 'var(--sg-navy-600)'}"></div>
      </div>
    </div>`;
  }).join('');

  document.getElementById('teacher-activity').innerHTML = teacherActivities.map(a => `
    <li class="flex items-start gap-3">
      <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:var(--sg-info-bg)"><i data-lucide="activity" class="w-4 h-4" style="color:var(--sg-navy-600)"></i></div>
      <div class="min-w-0"><p class="text-sm" style="color:var(--sg-text)">${a.text}</p><p class="text-xs" style="color:var(--sg-text-muted)">${a.time}</p></div>
    </li>
  `).join('');

  SG.renderIcons();
</script>
@endpush
