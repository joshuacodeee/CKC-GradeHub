@extends('layouts.app', ['role' => 'teacher', 'active' => 'classes', 'title' => 'My Classes'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">My Classes</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">First Semester, 2026–2027</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="classes-grid"></div>
</div>
@endsection

@push('scripts')
<script>
  // Mock data placeholder
</script>
@endpush
