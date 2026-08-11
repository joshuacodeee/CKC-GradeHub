@extends('layouts.app', ['role' => 'teacher', 'active' => 'reports', 'title' => 'Reports'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Class Reports</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">Generate a printable grade report for any of your classes</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="teacher-report-cards"></div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('teacher-report-cards').innerHTML = teacherClasses.map(c => {
    const rows = gradeSheets[`${c.subjectCode}-${c.section}`] || [];
    const avg = rows.length
      ? (rows.reduce((sum, r) => sum + SG.computeFinalGrade(r.prelim, r.midterm, r.final).grade, 0) / rows.length).toFixed(1)
      : '—';
    return `
    <div class="sg-card p-5">
      <span class="sg-badge sg-badge-info mb-2 inline-block">${c.subjectCode}</span>
      <h4 class="font-display font-semibold" style="color:var(--sg-text)">${c.subject}</h4>
      <p class="text-sm mb-3" style="color:var(--sg-text-muted)">${c.section} · Class Average: <span class="font-semibold" style="color:var(--sg-text)">${avg}</span></p>
      <div class="flex gap-2">
        <button class="sg-btn sg-btn-outline flex-1 text-sm" data-toast="Generating report for ${c.subject} — ${c.section}…" data-toast-type="info"><i data-lucide="file-text" class="w-4 h-4"></i> Preview</button>
        <button class="sg-btn sg-btn-primary text-sm !px-3" data-print title="Print"><i data-lucide="printer" class="w-4 h-4"></i></button>
      </div>
    </div>`;
  }).join('');
  SG.renderIcons();
</script>
@endpush
