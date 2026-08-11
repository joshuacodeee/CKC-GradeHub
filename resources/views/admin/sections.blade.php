@extends('layouts.app', ['role' => 'admin', 'active' => 'sections', 'title' => 'Sections'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Sections</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">32 active sections this semester</p>
  </div>

  <div class="sg-card p-5">
    @include('components.table', [
      'tableId' => 'sections-table',
      'searchPlaceholder' => 'Search by section name, course, or adviser…',
      'filterColumn' => 'status',
      'filterOptions' => ['Active', 'Inactive'],
      'addLabel' => 'Add Section',
      'addModalId' => 'add-section-modal',
      'addIcon' => 'layers',
    ])

    <div class="overflow-x-auto -mx-5 px-5">
      <table class="sg-table" id="sections-table">
        <thead>
          <tr>
            <th class="cursor-pointer" data-sort-column>Section Name</th>
            <th class="cursor-pointer" data-sort-column>Course</th>
            <th class="cursor-pointer" data-sort-column>Year Level</th>
            <th class="cursor-pointer" data-sort-column>Adviser</th>
            <th class="cursor-pointer" data-sort-column># Students</th>
            <th class="cursor-pointer" data-sort-column>Status</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

{{-- Add Section Modal --}}
<div id="add-section-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
  <div class="sg-modal sg-surface rounded-2xl w-full max-w-md">
    <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--sg-border)">
      <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Add New Section</h3>
      <button data-modal-close="add-section-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    <form id="add-section-form" class="p-6 space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Section Name</label>
        <input type="text" name="name" required placeholder="e.g. BSIT 1-B" class="sg-input">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Course</label>
          <select name="course" class="sg-select"><option>BSIT</option><option>BSCS</option></select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Year Level</label>
          <select name="year" class="sg-select"><option>1st Year</option><option>2nd Year</option><option>3rd Year</option><option>4th Year</option></select>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Adviser</label>
        <select name="adviser" class="sg-select">
          {{-- Options populated from mock teacher list via JS --}}
        </select>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" data-modal-close="add-section-modal" class="sg-btn sg-btn-outline">Cancel</button>
        <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save Section</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Populate adviser dropdown from mock teachers
  document.querySelector('#add-section-form select[name="adviser"]').innerHTML =
    teachers.map(t => `<option>${t.name}</option>`).join('');

  function renderSections() {
    const tbody = document.querySelector('#sections-table tbody');
    tbody.innerHTML = sections.map(s => `
      <tr>
        <td class="font-medium" style="color:var(--sg-text)">${s.name}</td>
        <td data-col="course" style="color:var(--sg-text-muted)">${s.course}</td>
        <td style="color:var(--sg-text-muted)">${s.year}</td>
        <td style="color:var(--sg-text-muted)">${s.adviser}</td>
        <td style="color:var(--sg-text)">${s.students}</td>
        <td data-col="status">${s.status === 'Active' ? '<span class="sg-badge sg-badge-success">Active</span>' : '<span class="sg-badge sg-badge-warning">Inactive</span>'}</td>
      </tr>
    `).join('');
  }
  renderSections();

  document.getElementById('add-section-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    const name = fd.get('name');
    sections.unshift({ name, course: fd.get('course'), year: fd.get('year'), adviser: fd.get('adviser'), students: 0, status: 'Active' });
    renderSections();
    SG.closeModal('add-section-modal');
    this.reset();
    SG.toast(`${name} was created (demo only).`, 'success');
  });
</script>
@endpush
