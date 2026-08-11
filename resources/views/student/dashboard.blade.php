@extends('layouts.app', ['role' => 'student', 'active' => 'dashboard', 'title' => 'Dashboard'])

@section('content')
<div class="space-y-6">

  {{-- Student info banner --}}
  <div class="sg-card p-6 flex flex-wrap items-center justify-between gap-6" style="background: linear-gradient(135deg, var(--sg-navy-800), var(--sg-navy-700)); color:#fff; border:none;">
    <div class="flex items-center gap-4">
      <div class="w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold flex-shrink-0" style="background:var(--sg-brass-500); color:var(--sg-navy-900)" data-current-user-initials>JD</div>
      <div>
        <h2 class="font-display font-bold text-xl" data-current-user-name>Juan Dela Cruz</h2>
        <p class="text-white/70 text-sm">2026-0001 · BSIT — 1st Year · BSIT 1-A</p>
      </div>
    </div>
    <div class="flex flex-wrap gap-6 text-sm">
      <div><p class="text-white/50 text-xs uppercase tracking-wide">School Year</p><p class="font-semibold">2026–2027</p></div>
      <div><p class="text-white/50 text-xs uppercase tracking-wide">Semester</p><p class="font-semibold">First Semester</p></div>
      <div><p class="text-white/50 text-xs uppercase tracking-wide">Current GWA</p><p class="font-semibold" style="color:var(--sg-brass-500)">90.4</p></div>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    @include('components.stat-card', ['label' => 'Enrolled Subjects', 'value' => '4', 'icon' => 'book-open', 'color' => 'navy'])
    @include('components.stat-card', ['label' => 'Units This Semester', 'value' => '12', 'icon' => 'layers', 'color' => 'brass'])
    @include('components.stat-card', ['label' => 'Status', 'value' => 'Active', 'icon' => 'badge-check', 'color' => 'success'])
  </div>

  {{-- Grade summary --}}
  <div class="sg-card p-5">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display font-semibold" style="color:var(--sg-text)">Grade Summary</h3>
      <a href="{{ route('student.grades') }}" class="text-xs font-medium" style="color:var(--sg-navy-500)">View full grade sheet</a>
    </div>
    <div class="overflow-x-auto">
      <table class="sg-table">
        <thead>
          <tr><th>Subject</th><th class="text-center">Prelim</th><th class="text-center">Midterm</th><th class="text-center">Final</th><th class="text-center">Final Grade</th><th>Remarks</th></tr>
        </thead>
        <tbody id="dash-grades-tbody"></tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('dash-grades-tbody').innerHTML = studentGrades.map(g => {
    const r = SG.computeFinalGrade(g.prelim, g.midterm, g.final);
    return `
    <tr>
      <td style="color:var(--sg-text)">${g.subject}</td>
      <td class="text-center" style="color:var(--sg-text-muted)">${g.prelim}</td>
      <td class="text-center" style="color:var(--sg-text-muted)">${g.midterm}</td>
      <td class="text-center" style="color:var(--sg-text-muted)">${g.final}</td>
      <td class="text-center font-semibold" style="color:var(--sg-text)">${r.grade.toFixed(2)}</td>
      <td>${r.passed ? '<span class="sg-badge sg-badge-success">Passed</span>' : '<span class="sg-badge sg-badge-danger">Failed</span>'}</td>
    </tr>`;
  }).join('');
</script>
@endpush
