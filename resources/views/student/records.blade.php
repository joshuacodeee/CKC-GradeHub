@extends('layouts.app', ['role' => 'student', 'active' => 'records', 'title' => 'Academic Records'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Academic Records</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">Completed semesters and grade history</p>
  </div>

  <div class="space-y-4" id="records-list"></div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('records-list').innerHTML = academicRecords.map(rec => `
    <div class="sg-card p-5">
      <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
        <div>
          <h4 class="font-display font-semibold" style="color:var(--sg-text)">${rec.schoolYear} · ${rec.semester}</h4>
          <p class="text-xs" style="color:var(--sg-text-muted)">GWA: <span class="font-semibold" style="color:var(--sg-text)">${rec.gwa}</span></p>
        </div>
        <span class="sg-badge sg-badge-info">${rec.status}</span>
      </div>
      <div class="overflow-x-auto">
        <table class="sg-table">
          <thead><tr><th>Code</th><th>Subject</th><th>Units</th><th class="text-center">Grade</th><th>Remarks</th></tr></thead>
          <tbody>
            ${rec.subjects.map(s => `
              <tr>
                <td class="font-medium" style="color:var(--sg-text)">${s.code}</td>
                <td style="color:var(--sg-text)">${s.subject}</td>
                <td style="color:var(--sg-text-muted)">${s.units}</td>
                <td class="text-center font-semibold" style="color:var(--sg-text)">${s.grade}</td>
                <td><span class="sg-badge sg-badge-success">${s.remarks}</span></td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    </div>
  `).join('');
</script>
@endpush
