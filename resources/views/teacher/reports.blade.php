@extends('layouts.app', ['role' => 'teacher', 'active' => 'reports', 'title' => 'Reports'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Class Reports</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">Generate a printable grade report for any of your classes</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="teacher-report-cards"></div>
</div>
@endsection

@push('scripts')
<script>
  // Mock data placeholder
</script>
@endpush
