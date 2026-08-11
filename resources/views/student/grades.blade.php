@extends('layouts.app', ['role' => 'student', 'active' => 'grades', 'title' => 'My Grades'])

@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">My Grades</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">First Semester, 2026–2027</p>
    </div>
    <button data-print class="sg-btn sg-btn-outline"><i data-lucide="printer" class="w-4 h-4"></i> Print</button>
  </div>

  <div class="sg-card p-5 print-area">
    <div class="overflow-x-auto">
      <table class="sg-table">
        <thead>
          <tr><th>Subject</th><th>Units</th><th class="text-center">Prelim</th><th class="text-center">Midterm</th><th class="text-center">Final</th><th class="text-center">Final Grade</th><th>Remarks</th></tr>
        </thead>
        <tbody id="grades-tbody"></tbody>
        <tfoot>
          <tr>
            <td class="font-semibold" colspan="5" style="color:var(--sg-text)">General Weighted Average</td>
            <td class="text-center font-bold" id="gwa-cell" style="color:var(--sg-text)">—</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  @include('components.notification', [
    'type' => 'info', 'title' => 'How your final grade is computed',
    'message' => 'Final Grade = (Prelim × 30%) + (Midterm × 30%) + (Final × 40%). A grade of 75 or higher is passing.',
  ])
</div>
@endsection

@push('scripts')
<script>
  let totalUnits = 0, totalPoints = 0;
  document.getElementById('grades-tbody').innerHTML = studentGrades.map(g => {
    const r = SG.computeFinalGrade(g.prelim, g.midterm, g.final);
    totalUnits += g.units;
    totalPoints += g.units * r.grade;
    return `
    <tr>
      <td style="color:var(--sg-text)">${g.subject} <span class="text-xs" style="color:var(--sg-text-muted)">(${g.code})</span><br><span class="text-xs" style="color:var(--sg-text-muted)">${g.instructor}</span></td>
      <td style="color:var(--sg-text-muted)">${g.units}</td>
      <td class="text-center" style="color:var(--sg-text-muted)">${g.prelim}</td>
      <td class="text-center" style="color:var(--sg-text-muted)">${g.midterm}</td>
      <td class="text-center" style="color:var(--sg-text-muted)">${g.final}</td>
      <td class="text-center font-semibold" style="color:var(--sg-text)">${r.grade.toFixed(2)}</td>
      <td>${r.passed ? '<span class="sg-badge sg-badge-success">Passed</span>' : '<span class="sg-badge sg-badge-danger">Failed</span>'}</td>
    </tr>`;
  }).join('');
  document.getElementById('gwa-cell').textContent = (totalPoints / totalUnits).toFixed(2);
</script>
@endpush
