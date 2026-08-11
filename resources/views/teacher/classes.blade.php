@extends('layouts.app', ['role' => 'teacher', 'active' => 'classes', 'title' => 'My Classes'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">My Classes</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">First Semester, 2026–2027</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="classes-grid"></div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('classes-grid').innerHTML = teacherClasses.map(c => `
    <div class="sg-card p-5">
      <div class="flex items-start justify-between mb-3">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:var(--sg-info-bg)">
          <i data-lucide="book-open" class="w-5 h-5" style="color:var(--sg-navy-600)"></i>
        </div>
        <span class="sg-badge sg-badge-info">${c.subjectCode}</span>
      </div>
      <h4 class="font-display font-semibold text-base" style="color:var(--sg-text)">${c.subject}</h4>
      <p class="text-sm mb-4" style="color:var(--sg-text-muted)">${c.section}</p>
      <div class="space-y-1.5 text-sm mb-4">
        <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="clock" class="w-4 h-4"></i> ${c.schedule}</p>
        <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="map-pin" class="w-4 h-4"></i> ${c.room}</p>
        <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="users" class="w-4 h-4"></i> ${c.students} Students</p>
      </div>
      <div class="flex gap-2">
        <a href="/teacher/grades?subject=${c.subjectCode}&section=${encodeURIComponent(c.section)}" class="sg-btn sg-btn-primary flex-1 text-sm">
          <i data-lucide="square-pen" class="w-4 h-4"></i> Enter Grades
        </a>
        <a href="/teacher/students" class="sg-btn sg-btn-outline text-sm !px-3"><i data-lucide="users" class="w-4 h-4"></i></a>
      </div>
    </div>
  `).join('');
  SG.renderIcons();
</script>
@endpush
