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
  // Mock data placeholder
</script>
@endpush
