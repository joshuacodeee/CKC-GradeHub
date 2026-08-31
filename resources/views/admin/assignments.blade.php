@extends('layouts.app', ['role' => 'admin', 'active' => 'assignments', 'title' => 'Teacher Assignments'])

@section('content')
  <div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Teacher Assignments</h2>
        <p class="text-sm" style="color:var(--sg-text-muted)">
          {{ $assignments->count() }} assignment{{ $assignments->count() != 1 ? 's' : '' }} on record
        </p>
      </div>
      <a href="{{ route('admin.assignments.create') }}" class="sg-btn sg-btn-primary">
        <i data-lucide="user-plus" class="w-4 h-4"></i> Assign Teacher
      </a>
    </div>

    <div class="sg-card p-5">
      {{-- Filters --}}
      <form method="GET" action="{{ route('admin.assignments') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3 mb-4">
        <select name="teacher_id" class="sg-select">
          <option value="">All Teachers</option>
          @foreach($teachers as $t)
            <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
          @endforeach
        </select>
        <select name="subject_id" class="sg-select">
          <option value="">All Subjects</option>
          @foreach($subjects as $s)
            <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->code }} — {{ $s->name }}</option>
          @endforeach
        </select>
        <select name="section_id" class="sg-select">
          <option value="">All Sections</option>
          @foreach($sections as $sec)
            <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
          @endforeach
        </select>
        <select name="semester" class="sg-select">
          <option value="">All Semesters</option>
          @foreach(['First Semester','Second Semester','Summer'] as $sem)
            <option value="{{ $sem }}" {{ request('semester') === $sem ? 'selected' : '' }}>{{ $sem }}</option>
          @endforeach
        </select>
        <select name="school_year_id" class="sg-select">
          <option value="">All School Years</option>
          @foreach($schoolYears as $sy)
            <option value="{{ $sy->id }}" {{ request('school_year_id') == $sy->id ? 'selected' : '' }}>{{ $sy->name }}</option>
          @endforeach
        </select>
      </form>

      <div class="overflow-x-auto">
        <table class="sg-table" id="assignments-table">
          <thead>
            <tr>
              <th>Teacher</th>
              <th>Subject</th>
              <th>Section</th>
              <th>School Year</th>
              <th>Semester</th>
              <th>Status</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($assignments as $a)
              <tr>
                <td class="font-medium" style="color:var(--sg-text)">{{ $a->teacher?->name ?? '—' }}</td>
                <td style="color:var(--sg-text)">{{ $a->subject?->code ?? '—' }} <span class="text-xs" style="color:var(--sg-text-muted)">{{ $a->subject?->name ?? '' }}</span></td>
                <td style="color:var(--sg-text-muted)">{{ $a->section?->name ?? '—' }}</td>
                <td style="color:var(--sg-text-muted)">{{ $a->schoolYear?->name ?? '—' }}</td>
                <td style="color:var(--sg-text-muted)">{{ $a->semester ?? '—' }}</td>
                <td>
                  <span class="sg-badge {{ $a->status === 'active' ? 'sg-badge-success' : 'sg-badge-info' }}">{{ ucfirst($a->status ?? 'active') }}</span>
                </td>
                <td class="text-right">
                  <div class="flex justify-end gap-1">
                    <a href="{{ route('admin.assignments.edit', $a) }}" class="sg-btn sg-btn-ghost !p-2" title="Edit">
                      <i data-lucide="pencil" class="w-4 h-4"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.assignments.destroy', $a) }}"
                      data-confirm="Remove this assignment? Grades already encoded remain linked to the teacher."
                      data-confirm-title="Remove Assignment">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="sg-btn sg-btn-ghost !p-2" title="Remove" style="color:var(--sg-danger)">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-10" style="color:var(--sg-text-muted)">No teacher assignments found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
