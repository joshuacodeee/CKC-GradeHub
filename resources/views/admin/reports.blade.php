@extends('layouts.app', ['role' => 'admin', 'active' => 'reports', 'title' => 'Grade Reports'])

@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Grade Reports</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">School-wide grade submissions, First Semester 2026–2027</p>
    </div>
    <div class="flex gap-2">
      <select class="sg-select sm:w-44">
        <option>All Sections</option>
        <option>BSIT 1-A</option><option>BSIT 2-A</option><option>BSCS 1-A</option>
      </select>
      <button data-print class="sg-btn sg-btn-outline"><i data-lucide="printer" class="w-4 h-4"></i> Print</button>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    @include('components.stat-card', ['label' => 'Grades Submitted', 'value' => '110', 'icon' => 'file-check-2', 'color' => 'success'])
    @include('components.stat-card', ['label' => 'Pending Submissions', 'value' => '15', 'icon' => 'clock', 'color' => 'warning'])
    @include('components.stat-card', ['label' => 'School-wide Average', 'value' => '87.6', 'icon' => 'chart-line', 'color' => 'navy'])
  </div>

  <div class="sg-card p-5 print-area">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display font-semibold" style="color:var(--sg-text)">Submission Log by Subject & Section</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="sg-table">
        <thead>
          <tr>
            <th>Subject</th><th>Section</th><th>Teacher</th><th># Students</th><th>Class Average</th><th>Status</th><th class="text-right no-print">Action</th>
          </tr>
        </thead>
        <tbody id="reports-tbody"></tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const reportRows = [
    { subject: 'Programming 1', code: 'IT102', section: 'BSIT 1-A', teacher: 'Prof. Andrea Reyes', students: 45, avg: 86.4, status: 'Submitted' },
    { subject: 'Database Management', code: 'IT201', section: 'BSIT 2-A', teacher: 'Prof. Miguel Santos', students: 38, avg: 89.1, status: 'Submitted' },
    { subject: 'Web Development', code: 'IT202', section: 'BSCS 1-A', teacher: 'Prof. Andrea Reyes', students: 42, avg: 84.7, status: 'Pending' },
    { subject: 'Networking', code: 'IT301', section: 'BSIT 3-A', teacher: 'Prof. Liza Domingo', students: 30, avg: 90.2, status: 'Submitted' },
  ];

  document.getElementById('reports-tbody').innerHTML = reportRows.map(r => `
    <tr>
      <td style="color:var(--sg-text)">${r.subject} <span class="text-xs" style="color:var(--sg-text-muted)">(${r.code})</span></td>
      <td style="color:var(--sg-text-muted)">${r.section}</td>
      <td style="color:var(--sg-text-muted)">${r.teacher}</td>
      <td style="color:var(--sg-text)">${r.students}</td>
      <td style="color:var(--sg-text)">${r.avg}</td>
      <td>${r.status === 'Submitted' ? '<span class="sg-badge sg-badge-success">Submitted</span>' : '<span class="sg-badge sg-badge-warning">Pending</span>'}</td>
      <td class="text-right no-print">
        <button class="sg-btn sg-btn-ghost !p-2" data-toast="Opening detailed report for ${r.subject} — ${r.section} (demo only)." data-toast-type="info"><i data-lucide="external-link" class="w-4 h-4"></i></button>
      </td>
    </tr>
  `).join('');
  SG.renderIcons();
</script>
@endpush
