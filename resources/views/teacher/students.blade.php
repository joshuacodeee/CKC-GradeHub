@extends('layouts.app', ['role' => 'teacher', 'active' => 'students', 'title' => 'Students'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Students in My Classes</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">All students across your assigned sections</p>
  </div>

  <div class="sg-card p-5">
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
      <div class="relative flex-1">
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color:var(--sg-text-muted)"></i>
        <input type="text" data-table-search="teacher-students-table" placeholder="Search students…" class="sg-input pl-9">
      </div>
      <select data-table-filter="teacher-students-table" data-filter-column="section" class="sg-select sm:w-48">
        <option value="">All Sections</option>
        <option>BSIT 1-A</option><option>BSIT 2-A</option><option>BSCS 1-A</option>
      </select>
    </div>
    <div class="overflow-x-auto">
      <table class="sg-table" id="teacher-students-table">
        <thead><tr><th>Student ID</th><th>Name</th><th>Section</th><th>Subject</th><th class="text-right">Action</th></tr></thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Mock data placeholder
</script>
@endpush
