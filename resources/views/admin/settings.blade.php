@extends('layouts.app', ['role' => 'admin', 'active' => 'settings', 'title' => 'Settings'])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6" data-tabs>

  {{-- Tab nav --}}
  <div class="lg:col-span-1">
    <div class="sg-card p-2 flex lg:flex-col gap-1 overflow-x-auto">
      <button data-tab-target="#tab-school-years" class="sg-tab-active sg-btn !justify-start w-full !bg-transparent" style="color:var(--sg-text)"><i data-lucide="calendar-range" class="w-4 h-4"></i> School Years</button>
      <button data-tab-target="#tab-grading" class="sg-btn !justify-start w-full !bg-transparent" style="color:var(--sg-text)"><i data-lucide="percent" class="w-4 h-4"></i> Grading Scale</button>
      <button data-tab-target="#tab-general" class="sg-btn !justify-start w-full !bg-transparent" style="color:var(--sg-text)"><i data-lucide="school" class="w-4 h-4"></i> School Info</button>
    </div>
  </div>

  {{-- Panels --}}
  <div class="lg:col-span-3 space-y-5">

    {{-- School Years --}}
    <div data-tab-panel id="tab-school-years">
      <div class="sg-card p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-display font-semibold" style="color:var(--sg-text)">School Years & Semesters</h3>
          <button data-modal-open="add-sy-modal" class="sg-btn sg-btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> Add School Year
          </button>
        </div>
        <div class="overflow-x-auto">
          <table class="sg-table">
            <thead>
              <tr>
                <th>School Year</th>
                <th>Semester</th>
                <th>Status</th>
                <th class="text-right no-print">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($schoolYears as $sy)
                <tr>
                  <td style="color:var(--sg-text)">{{ $sy->name }}</td>
                  <td style="color:var(--sg-text-muted)">{{ $sy->semester }}</td>
                  <td>
                    @if($sy->is_current)
                      <span class="sg-badge sg-badge-success">Current</span>
                    @elseif($sy->is_active)
                      <span class="sg-badge sg-badge-info">Active</span>
                    @else
                      <span class="sg-badge sg-badge-brass">Archived</span>
                    @endif
                  </td>
                  <td class="text-right no-print">
                    @if(!$sy->is_current)
                      <form method="POST" action="{{ route('admin.school-years.activate', $sy) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="sg-btn sg-btn-ghost !p-2" data-toast="Set as current school year." title="Set as current">
                          <i data-lucide="check" class="w-4 h-4"></i>
                        </button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-10" style="color:var(--sg-text-muted)">No school years defined.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Grading Scale --}}
    <div data-tab-panel id="tab-grading" class="hidden">
      <div class="sg-card p-5">
        <h3 class="font-display font-semibold mb-4" style="color:var(--sg-text)">Grade Weighting</h3>
        <p class="text-sm mb-4" style="color:var(--sg-text-muted)">Applied automatically when teachers enter Prelim, Midterm, and Final grades.</p>
        <form id="grading-form" class="grid">
          <div class="grid grid-cols-3 gap-4 mb-4">
            <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Prelim</label><div class="relative"><input type="number" value="30" class="sg-input pr-8"><span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm" style="color:var(--sg-text-muted)">%</span></div></div>
            <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Midterm</label><div class="relative"><input type="number" value="30" class="sg-input pr-8"><span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm" style="color:var(--sg-text-muted)">%</span></div></div>
            <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Final</label><div class="relative"><input type="number" value="40" class="sg-input pr-8"><span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm" style="color:var(--sg-text-muted)">%</span></div></div>
          </div>
          <div class="flex items-center gap-3 mb-4">
            <label class="text-sm font-medium" style="color:var(--sg-text)">Passing grade</label>
            <input type="number" value="75" class="sg-input !w-24">
          </div>
          <div class="flex justify-end">
            <button type="button" class="sg-btn sg-btn-primary" data-toast="Grading scale saved successfully."><i data-lucide="check" class="w-4 h-4"></i> Save Changes</button>
          </div>
        </form>
      </div>
    </div>

    {{-- School Info --}}
    <div data-tab-panel id="tab-general" class="hidden">
      <div class="sg-card p-5 space-y-4">
        <h3 class="font-display font-semibold" style="color:var(--sg-text)">School Information</h3>
        <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Name</label><input type="text" value="Christ the King College" class="sg-input"></div>
        <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Address</label><input type="text" value="123 Rizal Avenue, Imus, Cavite, Philippines" class="sg-input"></div>
        <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Contact Number</label><input type="text" value="0917-123-4567" class="sg-input"></div>
        <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email</label><input type="email" value="info@ckc.edu.ph" class="sg-input"></div>
        <div class="flex justify-end">
          <button type="button" class="sg-btn sg-btn-primary" data-toast="School information updated successfully."><i data-lucide="check" class="w-4 h-4"></i> Save Changes</button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Add School Year Modal --}}
<div id="add-sy-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
  <div class="sg-modal sg-surface rounded-2xl w-full max-w-md">
    <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--sg-border)">
      <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Add School Year</h3>
      <button data-modal-close="add-sy-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    <form method="POST" action="{{ route('admin.school-years.store') }}" class="p-6 space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Year Name</label>
        <input type="text" name="name" required placeholder="e.g. 2027–2028" class="sg-input">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Semester</label>
        <select name="semester" class="sg-select" required>
          <option value="First Semester">First Semester</option>
          <option value="Second Semester">Second Semester</option>
        </select>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" data-modal-close="add-sy-modal" class="sg-btn sg-btn-outline">Cancel</button>
        <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save</button>
      </div>
    </form>
  </div>
</div>
@endsection
