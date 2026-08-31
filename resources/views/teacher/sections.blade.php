@extends('layouts.app', ['role' => 'teacher', 'active' => 'sections', 'title' => 'My Sections'])

@section('content')
  <div class="space-y-5">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">My Sections</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">
        Sections assigned to you for {{ $schoolYear?->name ?? '—' }} — {{ $schoolYear?->semester ?? '—' }}
      </p>
    </div>

    @forelse ($groupedAssignments as $subjectId => $assignments)
      <div class="sg-card p-5">
        <div class="flex items-start justify-between mb-3">
          <div>
            <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">
              {{ $assignments->first()->subject?->code ?? '—' }} — {{ $assignments->first()->subject?->name ?? 'Unnamed' }}
            </h3>
            <p class="text-xs" style="color:var(--sg-text-muted)">
              {{ $assignments->count() }} section{{ $assignments->count() != 1 ? 's' : '' }} assigned
            </p>
          </div>
          <span class="sg-badge sg-badge-info">{{ $assignments->first()->subject?->units ?? 0 }} units</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          @foreach ($assignments as $a)
            <div class="rounded-lg border p-4" style="border-color:var(--sg-border)">
              <div class="flex items-center justify-between mb-1">
                <p class="font-medium text-sm" style="color:var(--sg-text)">{{ $a->section?->name ?? '—' }}</p>
                <span class="sg-badge sg-badge-success">{{ ucfirst($a->status ?? 'active') }}</span>
              </div>
              <p class="text-xs mb-3" style="color:var(--sg-text-muted)">
                {{ $a->section?->course ?? '' }} · {{ $a->section?->year_level ?? '' }}
              </p>
              <p class="text-xs mb-3" style="color:var(--sg-text-muted)">
                <i data-lucide="users" class="w-3 h-3 inline -mt-0.5"></i>
                {{ $a->section?->studentCount() ?? 0 }} students
              </p>
              <a href="{{ route('teacher.grades.enter', $a) }}" class="sg-btn sg-btn-primary w-full !py-1.5 !text-xs justify-center">
                <i data-lucide="square-pen" class="w-4 h-4"></i> Enter Grades
              </a>
            </div>
          @endforeach
        </div>
      </div>
    @empty
      <div class="sg-card p-8 text-center">
        <i data-lucide="layers" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
        <p class="text-sm" style="color:var(--sg-text-muted)">No sections assigned to you this semester.</p>
      </div>
    @endforelse
  </div>
@endsection
