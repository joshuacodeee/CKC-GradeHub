@extends('layouts.app', ['role' => 'admin', 'active' => 'students', 'title' => 'Students'])

@section('content')
<div class="space-y-5">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Student Records</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">1,248 total students across 32 sections</p>
    </div>
  </div>

  <div class="sg-card p-5">
    @include('components.table', [
      'tableId' => 'students-table',
      'searchPlaceholder' => 'Search by name, ID, or section…',
      'filterColumn' => 'status',
      'filterOptions' => ['Active', 'Inactive', 'Suspended'],
      'addLabel' => 'Add Student',
      'addModalId' => 'add-student-modal',
      'addIcon' => 'user-plus',
    ])

    <div class="overflow-x-auto -mx-5 px-5">
      <table class="sg-table" id="students-table">
        <thead>
          <tr>
            <th class="cursor-pointer" data-sort-column>Student ID</th>
            <th class="cursor-pointer" data-sort-column>Name</th>
            <th class="cursor-pointer" data-sort-column>Course</th>
            <th class="cursor-pointer" data-sort-column>Year Level</th>
            <th class="cursor-pointer" data-sort-column>Section</th>
            <th class="cursor-pointer" data-sort-column>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody id="students-tbody"></tbody>
      </table>
      <div data-empty-state="students-table" class="hidden text-center py-12">
        <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
        <p class="text-sm" style="color:var(--sg-text-muted)">No students match your search.</p>
      </div>
    </div>

    <div class="flex items-center justify-between mt-4 pt-4 border-t text-sm" style="border-color:var(--sg-border)">
      <span style="color:var(--sg-text-muted)">Showing 1–12 of 1,248 students</span>
      <div class="flex items-center gap-1">
        <button class="sg-btn sg-btn-outline !px-3 !py-1.5"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
        <button class="sg-btn sg-btn-primary !px-3 !py-1.5">1</button>
        <button class="sg-btn sg-btn-outline !px-3 !py-1.5">2</button>
        <button class="sg-btn sg-btn-outline !px-3 !py-1.5">3</button>
        <button class="sg-btn sg-btn-outline !px-3 !py-1.5"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
      </div>
    </div>
  </div>
</div>

{{-- Add Student Modal --}}
<div id="add-student-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
  <div class="sg-modal sg-surface rounded-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
    <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0" style="border-color:var(--sg-border)">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:var(--sg-info-bg)"><i data-lucide="user-plus" class="w-4 h-4" style="color:var(--sg-navy-600)"></i></div>
        <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Add New Student</h3>
      </div>
      <button data-modal-close="add-student-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    <form id="add-student-form" class="p-6 overflow-y-auto space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Full Name</label>
          <input type="text" name="name" required placeholder="e.g. Juan Dela Cruz" class="sg-input">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Course</label>
          <select name="course" class="sg-select" required>
            <option value="BSIT">BSIT</option>
            <option value="BSCS">BSCS</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Year Level</label>
          <select name="year" class="sg-select" required>
            <option>1st Year</option><option>2nd Year</option><option>3rd Year</option><option>4th Year</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Section</label>
          <input type="text" name="section" required placeholder="e.g. BSIT 1-A" class="sg-input">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Status</label>
          <select name="status" class="sg-select">
            <option>Active</option><option>Inactive</option>
          </select>
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email Address</label>
          <input type="email" name="email" required placeholder="student@nbit.edu.ph" class="sg-input">
        </div>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" data-modal-close="add-student-modal" class="sg-btn sg-btn-outline">Cancel</button>
        <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save Student</button>
      </div>
    </form>
  </div>
</div>

{{-- View Student Modal --}}
<div id="view-student-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
  <div class="sg-modal sg-surface rounded-2xl w-full max-w-md">
    <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--sg-border)">
      <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Student Details</h3>
      <button data-modal-close="view-student-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    <div class="p-6 space-y-3 text-sm" id="view-student-body"></div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  function statusBadge(status) {
    const map = { Active: 'success', Inactive: 'warning', Suspended: 'danger' };
    return `<span class="sg-badge sg-badge-${map[status] || 'info'}">${status}</span>`;
  }

  function renderStudents() {
    document.getElementById('students-tbody').innerHTML = students.map(s => `
      <tr data-grade-row="false">
        <td class="font-medium" data-col="id" style="color:var(--sg-text)">${s.id}</td>
        <td style="color:var(--sg-text)">${s.name}</td>
        <td data-col="course" style="color:var(--sg-text-muted)">${s.course}</td>
        <td data-col="year" style="color:var(--sg-text-muted)">${s.year}</td>
        <td data-col="section" style="color:var(--sg-text-muted)">${s.section}</td>
        <td data-col="status">${statusBadge(s.status)}</td>
        <td class="text-right">
          <div class="inline-flex items-center gap-1">
            <button class="sg-btn sg-btn-ghost !p-2" title="View" data-view-student="${s.id}"><i data-lucide="eye" class="w-4 h-4"></i></button>
            <button class="sg-btn sg-btn-ghost !p-2" title="Edit" data-toast="Edit form would open here for ${s.name} (demo only)." data-toast-type="info"><i data-lucide="pencil" class="w-4 h-4"></i></button>
            <button class="sg-btn sg-btn-ghost !p-2" title="Delete" data-delete-row="${s.name}" style="color:var(--sg-danger)"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
          </div>
        </td>
      </tr>
    `).join('');
    SG.renderIcons();

    document.querySelectorAll('[data-view-student]').forEach(btn => {
      btn.addEventListener('click', () => {
        const s = students.find(x => x.id === btn.getAttribute('data-view-student'));
        if (!s) return;
        document.getElementById('view-student-body').innerHTML = `
          <div class="flex items-center gap-3 mb-2">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold" style="background:var(--sg-navy-700)">${s.name.split(' ').map(n=>n[0]).slice(0,2).join('')}</div>
            <div><p class="font-semibold" style="color:var(--sg-text)">${s.name}</p><p class="text-xs" style="color:var(--sg-text-muted)">${s.id}</p></div>
          </div>
          <div class="grid grid-cols-2 gap-3 pt-2">
            <div><p class="text-xs" style="color:var(--sg-text-muted)">Course</p><p style="color:var(--sg-text)">${s.course}</p></div>
            <div><p class="text-xs" style="color:var(--sg-text-muted)">Year Level</p><p style="color:var(--sg-text)">${s.year}</p></div>
            <div><p class="text-xs" style="color:var(--sg-text-muted)">Section</p><p style="color:var(--sg-text)">${s.section}</p></div>
            <div><p class="text-xs" style="color:var(--sg-text-muted)">Status</p><p>${statusBadge(s.status)}</p></div>
            <div class="col-span-2"><p class="text-xs" style="color:var(--sg-text-muted)">Email</p><p style="color:var(--sg-text)">${s.email}</p></div>
          </div>
        `;
        SG.openModal('view-student-modal');
      });
    });
  }
  renderStudents();

  document.getElementById('add-student-form').addEventListener('submit', function (e) {
    e.preventDefault();
    if (!window.SG) return;
    const fd = new FormData(this);
    const name = fd.get('name');
    students.unshift({
      id: '2026-' + String(Math.floor(1000 + Math.random() * 8999)),
      name, course: fd.get('course'), year: fd.get('year'), section: fd.get('section'),
      status: fd.get('status'), email: fd.get('email'),
    });
    renderStudents();
    SG.closeModal('add-student-modal');
    this.reset();
    SG.toast(`${name} was added (demo only — no data was actually saved).`, 'success');
  });
</script>
@endpush
