@extends('layouts.app', ['role' => 'registrar', 'active' => 'dashboard', 'title' => 'Registrar Dashboard'])

@section('content')
  <div class="space-y-6">
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8"
      style="background:linear-gradient(140deg, var(--sg-navy-900), var(--sg-navy-700)); color:#fff;">
      <div class="flex flex-wrap items-end justify-between gap-6">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.22em] mb-3" style="color:var(--sg-brass-500)">
            CKC GradeHub · Registrar
          </p>
          <h1 class="font-display text-3xl sm:text-4xl font-bold">Student records & academic history</h1>
          <p class="mt-3 text-sm text-white/70">
            {{ $schoolYear?->name ?? 'No active school year' }}
            @if ($schoolYear) · {{ $schoolYear->semester }} @endif
          </p>
        </div>
        <a href="{{ route('registrar.students') }}" class="sg-btn !px-4 !py-2"
          style="background:var(--sg-brass-500); color:var(--sg-navy-900)">
          <i data-lucide="search" class="w-4 h-4"></i> Find a student
        </a>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      @include('components.stat-card', ['label' => 'Student records', 'value' => $studentCount, 'icon' => 'users', 'color' => 'navy'])
      @include('components.stat-card', ['label' => 'Irregular students', 'value' => $irregularCount, 'icon' => 'user-round-cog', 'color' => 'warning'])
      @include('components.stat-card', ['label' => 'Individual subject enrollments', 'value' => $enrollmentCount, 'icon' => 'book-check', 'color' => 'success'])
    </div>

    <div class="sg-card p-5">
      <div class="flex items-center justify-between gap-3 mb-4">
        <div>
          <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Irregular students</h2>
          <p class="text-sm" style="color:var(--sg-text-muted)">Open a student record to manage subject assignments and grades.</p>
        </div>
        <a href="{{ route('registrar.students', ['standing' => 'irregular']) }}" class="sg-btn sg-btn-outline">View all</a>
      </div>
      <div class="overflow-x-auto">
        <table class="sg-table">
          <thead><tr><th>Student ID</th><th>Name</th><th>Course / Year</th><th></th></tr></thead>
          <tbody>
            @forelse ($recentIrregularStudents as $student)
              <tr>
                <td>{{ $student->student_id ?? '—' }}</td>
                <td>{{ $student->last_name_first }}</td>
                <td>{{ $student->course ?? '—' }} · {{ $student->year_level ?? '—' }}</td>
                <td class="text-right">
                  <a href="{{ route('registrar.students.show', $student) }}" class="sg-btn sg-btn-ghost !px-2 !py-1">
                    View record <i data-lucide="arrow-right" class="w-4 h-4"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center py-8" style="color:var(--sg-text-muted)">No irregular students yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
