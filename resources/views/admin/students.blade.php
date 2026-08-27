@extends('layouts.app', ['role' => 'admin', 'active' => 'students', 'title' => 'Students'])

@section('content')
  <div class="space-y-5">

    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Student Records</h2>
        <p class="text-sm" style="color:var(--sg-text-muted)">{{ $students->count() }} registered student accounts</p>
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
          <tbody id="students-tbody">
            @forelse ($students as $student)
              <tr>
                <td style="color:var(--sg-text)">{{ $student->student_id }}</td>
                <td class="font-medium" style="color:var(--sg-text)">{{ $student->name }}</td>
                <td style="color:var(--sg-text-muted)">{{ $student->course ?? '—' }}</td>
                <td style="color:var(--sg-text-muted)">{{ $student->year_level ?? '—' }}</td>
                <td style="color:var(--sg-text-muted)">—</td>
                <td><span
                    class="sg-badge {{ $student->is_active ? 'sg-badge-success' : 'sg-badge-danger' }}">{{ $student->is_active ? 'Active' : 'Inactive' }}</span>
                </td>
                <td class="text-right" style="color:var(--sg-text-muted)">{{ $student->email }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-10" style="color:var(--sg-text-muted)">No student accounts have been
                  created yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
        <div data-empty-state="students-table" class="hidden text-center py-12">
          <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
          <p class="text-sm" style="color:var(--sg-text-muted)">No students match your search.</p>
        </div>
      </div>

      <div class="flex items-center justify-between mt-4 pt-4 border-t text-sm" style="border-color:var(--sg-border)">
        <span style="color:var(--sg-text-muted)">Showing {{ $students->count() }} student accounts</span>
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
      <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0"
        style="border-color:var(--sg-border)">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:var(--sg-info-bg)"><i
              data-lucide="user-plus" class="w-4 h-4" style="color:var(--sg-navy-600)"></i></div>
          <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Add New Student</h3>
        </div>
        <button data-modal-close="add-student-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x"
            class="w-4 h-4"></i></button>
      </div>
      <form id="add-student-form" method="POST" action="{{ route('admin.students.store') }}"
        class="p-6 overflow-y-auto space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Full Name</label>
            <input type="text" name="name" required placeholder="e.g. Juan Dela Cruz" class="sg-input">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Student ID</label>
            <input type="text" name="student_id" required placeholder="e.g. 2026-0001" class="sg-input">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Course</label>
            <select name="course" class="sg-select" required>
              @foreach (['BSIT', 'BSSW', 'BSCRIM', 'BSED', 'BSA', 'BSBA', 'BSN', 'BSHM'] as $course)
                <option value="{{ $course }}">{{ $course }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Year Level</label>
            <select name="year_level" class="sg-select" required>
              <option>1st Year</option>
              <option>2nd Year</option>
              <option>3rd Year</option>
              <option>4th Year</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Password</label>
          <input type="password" name="password" required minlength="8" class="sg-input">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Confirm Password</label>
          <input type="password" name="password_confirmation" required minlength="8" class="sg-input">
        </div>
    </div>
    <div class="flex justify-end gap-2 pt-2">
      <button type="button" data-modal-close="add-student-modal" class="sg-btn sg-btn-outline">Cancel</button>
      <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save
        Student</button>
    </div>
    </form>
  </div>
  </div>

  {{-- View Student Modal --}}
  <div id="view-student-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
    <div class="sg-modal sg-surface rounded-2xl w-full max-w-md">
      <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--sg-border)">
        <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Student Details</h3>
        <button data-modal-close="view-student-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x"
            class="w-4 h-4"></i></button>
      </div>
      <div class="p-6 space-y-3 text-sm" id="view-student-body"></div>
    </div>
  </div>

@endsection