@extends('layouts.app', ['role' => 'student', 'active' => 'dashboard', 'title' => 'Dashboard'])

@section('content')
<div class="space-y-6">

  {{-- Student info banner --}}
  @php($student = $user)
  <div class="sg-card p-6 flex flex-wrap items-center justify-between gap-6"
    style="background: linear-gradient(135deg, var(--sg-navy-800), var(--sg-navy-700)); color:#fff; border:none;">
    <div class="flex items-center gap-4">
      <div class="w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold flex-shrink-0"
        style="background:var(--sg-brass-500); color:var(--sg-navy-900)" data-current-user-initials>
        {{ collect(explode(' ', $student->name))->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->join('') }}
      </div>
      <div>
        <h2 class="font-display font-bold text-xl" data-current-user-name>{{ $student->name }}</h2>
        <p class="text-white/70 text-sm">{{ $student->student_id }}</p>
      </div>
    </div>
    <div class="flex flex-wrap gap-6 text-sm">
      <div>
        <p class="text-white/50 text-xs uppercase tracking-wide">School Year</p>
        <p class="font-semibold">{{ $schoolYear?->name ?? '2026–2027' }}</p>
      </div>
      <div>
        <p class="text-white/50 text-xs uppercase tracking-wide">Semester</p>
        <p class="font-semibold">{{ $schoolYear?->semester ?? 'First Semester' }}</p>
      </div>
      <div>
        <p class="text-white/50 text-xs uppercase tracking-wide">Current GWA</p>
        <p class="font-semibold" style="color:var(--sg-brass-500)">{{ $gwa ?? '—' }}</p>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    @include('components.stat-card', ['label' => 'Enrolled Subjects', 'value' => $enrolledSubjects->count(), 'icon' => 'book-open', 'color' => 'navy'])
    @include('components.stat-card', ['label' => 'Units This Semester', 'value' => $totalUnits, 'icon' => 'layers', 'color' => 'brass'])
    <div class="sg-card p-5 flex items-start justify-between">
      <div class="min-w-0">
        <p class="text-sm font-medium" style="color:var(--sg-text-muted)">Enrollment Status</p>
        <p class="font-display text-2xl sm:text-3xl font-bold mt-1" style="color:var(--sg-text)">{{ $section ? 'Enrolled' : 'Not Enrolled' }}</p>
      </div>
      <div class="sg-stat-icon" style="background:var(--sg-success-bg)">
        <i data-lucide="{{ $section ? 'badge-check' : 'alert-circle' }}" class="w-5 h-5" style="color:var(--sg-success)"></i>
      </div>
    </div>
  </div>

  {{-- Grade summary --}}
  <div class="sg-card p-5">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display font-semibold" style="color:var(--sg-text)">Grade Summary</h3>
      <a href="{{ route('student.grades') }}" class="text-xs font-medium" style="color:var(--sg-navy-500)">View full
        grade sheet</a>
    </div>
    <div class="overflow-x-auto">
      <table class="sg-table">
        <thead>
          <tr>
            <th>Subject</th>
            <th class="text-center">Prelim</th>
            <th class="text-center">Midterm</th>
            <th class="text-center">Final</th>
            <th class="text-center">Final Grade</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($grades as $grade)
            <tr>
              <td class="font-medium" style="color:var(--sg-text)">{{ $grade->subject->code ?? '—' }}</td>
              <td class="text-center" style="color:var(--sg-text-muted)">{{ $grade->prelim ?? '—' }}</td>
              <td class="text-center" style="color:var(--sg-text-muted)">{{ $grade->midterm ?? '—' }}</td>
              <td class="text-center" style="color:var(--sg-text-muted)">{{ $grade->final ?? '—' }}</td>
              <td class="text-center font-medium" style="color:var(--sg-text)">{{ $grade->final_grade ?? '—' }}</td>
              <td>
                @if($grade->remarks)
                  @if($grade->remarks === 'Passed')
                    <span class="sg-badge sg-badge-success">{{ $grade->remarks }}</span>
                  @else
                    <span class="sg-badge sg-badge-danger">{{ $grade->remarks }}</span>
                  @endif
                @else
                  <span class="sg-badge sg-badge-info">Pending</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-10" style="color:var(--sg-text-muted)">No grades recorded yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
