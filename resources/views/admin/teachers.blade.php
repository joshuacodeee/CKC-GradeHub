@extends('layouts.app', ['role' => 'admin', 'active' => 'teachers', 'title' => 'Teachers'])

@section('content')
  <div class="space-y-5">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Teacher Records</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">{{ $teachers->count() }} registered teacher accounts</p>
    </div>

    <div class="sg-card p-5">
      @include('components.table', [
        'tableId' => 'teachers-table',
        'searchPlaceholder' => 'Search by name, ID, or department…',
        'filterColumn' => 'status',
        'filterOptions' => ['Active', 'On Leave'],
        'addLabel' => 'Add Teacher',
        'addModalId' => 'add-teacher-modal',
        'addIcon' => 'user-plus',
      ])

      <div class="overflow-x-auto -mx-5 px-5">
        <table class="sg-table" id="teachers-table">
          <thead>
            <tr>
              <th class="cursor-pointer" data-sort-column>Teacher ID</th>
              <th class="cursor-pointer" data-sort-column>Name</th>
              <th class="cursor-pointer" data-sort-column>Email</th>
              <th class="cursor-pointer" data-sort-column>Department</th>
              <th>Assigned Subjects</th>
              <th class="cursor-pointer" data-sort-column>Status</th>
              <th class="text-right">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($teachers as $teacher)
              <tr>
                <td style="color:var(--sg-text)">{{ $teacher->id }}</td>
                <td class="font-medium" style="color:var(--sg-text)">{{ $teacher->name }}</td>
                <td style="color:var(--sg-text-muted)">{{ $teacher->email }}</td>
                <td style="color:var(--sg-text-muted)">—</td>
                <td style="color:var(--sg-text-muted)">—</td>
                <td class="text-right"><span
                    class="sg-badge {{ $teacher->is_active ? 'sg-badge-success' : 'sg-badge-danger' }}">{{ $teacher->is_active ? 'Active' : 'Inactive' }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-10" style="color:var(--sg-text-muted)">No teacher accounts have been
                  created yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
        <div data-empty-state="teachers-table" class="hidden text-center py-12">
          <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
          <p class="text-sm" style="color:var(--sg-text-muted)">No teachers match your search.</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Add Teacher Modal --}}
  <div id="add-teacher-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
    <div class="sg-modal sg-surface rounded-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
      <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0"
        style="border-color:var(--sg-border)">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:var(--sg-info-bg)"><i
              data-lucide="user-plus" class="w-4 h-4" style="color:var(--sg-navy-600)"></i></div>
          <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Add New Teacher</h3>
        </div>
        <button data-modal-close="add-teacher-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x"
            class="w-4 h-4"></i></button>
      </div>
      <form id="add-teacher-form" method="POST" action="{{ route('admin.teachers.store') }}"
        class="p-6 overflow-y-auto space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Full Name</label>
            <input type="text" name="name" required placeholder="e.g. Prof. Andrea Reyes" class="sg-input">
          </div>
          <div class="col-span-2">
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email Address</label>
            <input type="email" name="email" required placeholder="name@nbit.edu.ph" class="sg-input">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Password</label>
            <input type="password" name="password" required minlength="8" class="sg-input">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Confirm Password</label>
            <input type="password" name="password_confirmation" required minlength="8" class="sg-input">
          </div>
          <div class="col-span-2">
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Department</label>
            <select name="department" class="sg-select" required>
              <option>College of Computer Studies</option>
              <option>General Education</option>
              <option>College of Business</option>
            </select>
          </div>
          <div class="col-span-2">
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Status</label>
            <select name="status" class="sg-select">
              <option>Active</option>
              <option>On Leave</option>
            </select>
          </div>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" data-modal-close="add-teacher-modal" class="sg-btn sg-btn-outline">Cancel</button>
          <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save
            Teacher</button>
        </div>
      </form>
    </div>
  </div>
@endsection