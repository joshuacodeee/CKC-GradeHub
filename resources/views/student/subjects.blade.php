@extends('layouts.app', ['role' => 'student', 'active' => 'subjects', 'title' => 'My Subjects'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Enrolled Subjects</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">First Semester, 2026–2027 · 12 total units</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="subjects-grid"></div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('subjects-grid').innerHTML = studentGrades.map(g => `
    <div class="sg-card p-5 flex items-start gap-4">
      <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:var(--sg-info-bg)">
        <i data-lucide="book-open" class="w-5 h-5" style="color:var(--sg-navy-600)"></i>
      </div>
      <div class="min-w-0">
        <div class="flex items-center gap-2 mb-1">
          <h4 class="font-display font-semibold" style="color:var(--sg-text)">${g.subject}</h4>
          <span class="sg-badge sg-badge-info">${g.code}</span>
        </div>
        <p class="text-sm" style="color:var(--sg-text-muted)">${g.units} Units · ${g.instructor}</p>
      </div>
    </div>
  `).join('');
  SG.renderIcons();
</script>
@endpush
