@extends('layouts.app', ['role' => 'teacher', 'active' => 'subjects', 'title' => 'My Subjects'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">My Subjects</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">Subjects assigned to you this semester</p>
  </div>

  <div class="sg-card p-5">
    <div class="overflow-x-auto">
      <table class="sg-table">
        <thead><tr><th>Code</th><th>Subject Name</th><th>Units</th><th>Sections Handled</th><th>Total Students</th></tr></thead>
        <tbody id="teacher-subjects-tbody"></tbody>
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
