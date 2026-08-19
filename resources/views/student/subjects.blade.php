@extends('layouts.app', ['role' => 'student', 'active' => 'subjects', 'title' => 'My Subjects'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Enrolled Subjects</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">First Semester, 2026–2027 · 12 total units</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="subjects-grid"></div>
</div>
@endsection

@push('scripts')
<script>
  // Mock data placeholder
</script>
@endpush
