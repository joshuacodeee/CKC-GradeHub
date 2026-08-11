@extends('layouts.app', ['role' => 'teacher', 'active' => 'grades', 'title' => 'Enter Grades'])

@section('content')
<div class="space-y-5">

  {{-- Class selector --}}
  <div class="sg-card p-5">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Class</label>
        <select id="class-selector" class="sg-select"></select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Year</label>
        <input type="text" value="2026–2027" class="sg-input" disabled>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Semester</label>
        <input type="text" value="First Semester" class="sg-input" disabled>
      </div>
    </div>
  </div>

  {{-- Class header --}}
  <div class="sg-card p-5 flex flex-wrap items-center justify-between gap-4">
    <div>
      <h2 id="gh-subject" class="font-display font-semibold text-lg" style="color:var(--sg-text)">—</h2>
      <p id="gh-section" class="text-sm" style="color:var(--sg-text-muted)">—</p>
    </div>
    <div class="flex items-center gap-2">
      <span id="gh-count" class="sg-badge sg-badge-info">0 Students</span>
      <span class="sg-badge sg-badge-warning">Weighting: 30% / 30% / 40%</span>
    </div>
  </div>

  {{-- Grade entry table --}}
  <div class="sg-card p-5">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display font-semibold" style="color:var(--sg-text)">Grade Sheet</h3>
      <span class="text-xs" style="color:var(--sg-text-muted)">Final grade auto-calculates as you type</span>
    </div>
    <div class="overflow-x-auto">
      <table class="sg-table">
        <thead>
          <tr>
            <th>Student ID</th><th>Student Name</th>
            <th class="text-center">Prelim</th><th class="text-center">Midterm</th><th class="text-center">Final</th>
            <th class="text-center">Final Grade</th><th>Remarks</th>
          </tr>
        </thead>
        <tbody id="grade-sheet-tbody"></tbody>
      </table>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-2 mt-5 pt-5 border-t no-print" style="border-color:var(--sg-border)">
      <button id="btn-print" class="sg-btn sg-btn-outline"><i data-lucide="printer" class="w-4 h-4"></i> Print</button>
      <button id="btn-save-draft" class="sg-btn sg-btn-outline"><i data-lucide="save" class="w-4 h-4"></i> Save Draft</button>
      <button id="btn-submit" class="sg-btn sg-btn-primary"><i data-lucide="send" class="w-4 h-4"></i> Submit Grades</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const classSelector = document.getElementById('class-selector');
  classSelector.innerHTML = teacherClasses.map(c =>
    `<option value="${c.subjectCode}|${c.section}">${c.subject} — ${c.section}</option>`
  ).join('');

  // Preselect via ?subject=&section= query params if present
  const params = new URLSearchParams(window.location.search);
  const preselect = params.get('subject') && params.get('section') ? `${params.get('subject')}|${params.get('section')}` : null;
  if (preselect && [...classSelector.options].some(o => o.value === preselect)) {
    classSelector.value = preselect;
  }

  function loadClass() {
    const [code, section] = classSelector.value.split('|');
    const cls = teacherClasses.find(c => c.subjectCode === code && c.section === section);
    const rows = gradeSheets[`${code}-${section}`] || [];

    document.getElementById('gh-subject').textContent = cls ? cls.subject : code;
    document.getElementById('gh-section').textContent = section;
    document.getElementById('gh-count').textContent = `${rows.length} Students`;

    document.getElementById('grade-sheet-tbody').innerHTML = rows.map(r => `
      <tr data-grade-row data-student-id="${r.id}">
        <td class="font-medium" style="color:var(--sg-text)">${r.id}</td>
        <td style="color:var(--sg-text)">${r.name}</td>
        <td class="text-center"><input type="number" min="0" max="100" class="sg-input grade-input" value="${r.prelim}"></td>
        <td class="text-center"><input type="number" min="0" max="100" class="sg-input grade-input" value="${r.midterm}"></td>
        <td class="text-center"><input type="number" min="0" max="100" class="sg-input grade-input" value="${r.final}"></td>
        <td class="text-center font-semibold" data-final-grade style="color:var(--sg-text)">—</td>
        <td data-remarks></td>
      </tr>
    `).join('');

    // Re-bind grade calculation listeners for the newly rendered rows
    document.querySelectorAll('[data-grade-row]').forEach(row => {
      row.querySelectorAll('.grade-input').forEach(input => {
        input.addEventListener('input', () => recalcRow(row));
      });
      recalcRow(row);
    });
  }

  function recalcRow(row) {
    const [prelim, midterm, final] = row.querySelectorAll('.grade-input');
    const result = SG.computeFinalGrade(prelim.value, midterm.value, final.value);
    const finalCell = row.querySelector('[data-final-grade]');
    const remarksCell = row.querySelector('[data-remarks]');
    if (!result) { finalCell.textContent = '—'; remarksCell.innerHTML = ''; return; }
    finalCell.textContent = result.grade.toFixed(2);
    remarksCell.innerHTML = result.passed
      ? `<span class="sg-badge sg-badge-success">Passed</span>`
      : `<span class="sg-badge sg-badge-danger">Failed</span>`;
  }

  classSelector.addEventListener('change', loadClass);
  loadClass();

  document.getElementById('btn-save-draft').addEventListener('click', () => {
    SG.toast('Grades saved as draft successfully.', 'success');
  });
  document.getElementById('btn-submit').addEventListener('click', async () => {
    const ok = await SG.confirmAction('Once submitted, grades will be locked for editing unless a correction request is filed. Continue?', {
      title: 'Submit final grades?', confirmText: 'Submit', danger: false,
    });
    if (ok) SG.toast('Grades submitted successfully.', 'success');
  });
  document.getElementById('btn-print').addEventListener('click', () => window.print());
</script>
@endpush
