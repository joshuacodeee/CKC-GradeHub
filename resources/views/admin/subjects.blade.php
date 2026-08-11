@extends('layouts.app', ['role' => 'admin', 'active' => 'subjects', 'title' => 'Subjects'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Subject Catalog</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">54 subjects offered this semester</p>
  </div>

  <div class="sg-card p-5">
    @include('components.table', [
      'tableId' => 'subjects-table',
      'searchPlaceholder' => 'Search by subject code or name…',
      'filterColumn' => 'status',
      'filterOptions' => ['Active', 'Inactive'],
      'addLabel' => 'Add Subject',
      'addModalId' => 'add-subject-modal',
      'addIcon' => 'book-plus',
    ])

    <div class="overflow-x-auto -mx-5 px-5">
      <table class="sg-table" id="subjects-table">
        <thead>
          <tr>
            <th class="cursor-pointer" data-sort-column>Subject Code</th>
            <th class="cursor-pointer" data-sort-column>Subject Name</th>
            <th class="cursor-pointer" data-sort-column>Units</th>
            <th class="cursor-pointer" data-sort-column>Department</th>
            <th class="cursor-pointer" data-sort-column>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <div data-empty-state="subjects-table" class="hidden text-center py-12">
        <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
        <p class="text-sm" style="color:var(--sg-text-muted)">No subjects match your search.</p>
      </div>
    </div>
  </div>
</div>

{{-- Add Subject Modal --}}
<div id="add-subject-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
  <div class="sg-modal sg-surface rounded-2xl w-full max-w-md">
    <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--sg-border)">
      <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Add New Subject</h3>
      <button data-modal-close="add-subject-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    <form id="add-subject-form" class="p-6 space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Subject Code</label>
        <input type="text" name="code" required placeholder="e.g. IT401" class="sg-input">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Subject Name</label>
        <input type="text" name="name" required placeholder="e.g. Systems Integration" class="sg-input">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Units</label>
          <input type="number" name="units" min="1" max="6" required value="3" class="sg-input">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Status</label>
          <select name="status" class="sg-select"><option>Active</option><option>Inactive</option></select>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Department</label>
        <select name="department" class="sg-select">
          <option>College of Computer Studies</option>
          <option>General Education</option>
        </select>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" data-modal-close="add-subject-modal" class="sg-btn sg-btn-outline">Cancel</button>
        <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save Subject</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function renderSubjects() {
    const tbody = document.querySelector('#subjects-table tbody');
    tbody.innerHTML = subjects.map(s => `
      <tr>
        <td class="font-medium" style="color:var(--sg-text)">${s.code}</td>
        <td style="color:var(--sg-text)">${s.name}</td>
        <td style="color:var(--sg-text-muted)">${s.units}</td>
        <td data-col="department" style="color:var(--sg-text-muted)">${s.department}</td>
        <td data-col="status">${s.status === 'Active' ? '<span class="sg-badge sg-badge-success">Active</span>' : '<span class="sg-badge sg-badge-warning">Inactive</span>'}</td>
        <td class="text-right">
          <div class="inline-flex items-center gap-1">
            <button class="sg-btn sg-btn-ghost !p-2" title="Edit" data-toast="Edit form would open here for ${s.name} (demo only)." data-toast-type="info"><i data-lucide="pencil" class="w-4 h-4"></i></button>
            <button class="sg-btn sg-btn-ghost !p-2" title="Delete" data-delete-row="${s.name}" style="color:var(--sg-danger)"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
          </div>
        </td>
      </tr>
    `).join('');
    SG.renderIcons();
  }
  renderSubjects();

  document.getElementById('add-subject-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    const name = fd.get('name');
    subjects.unshift({ code: fd.get('code'), name, units: fd.get('units'), department: fd.get('department'), status: fd.get('status') });
    renderSubjects();
    SG.closeModal('add-subject-modal');
    this.reset();
    SG.toast(`${name} was added to the subject catalog (demo only).`, 'success');
  });
</script>
@endpush
