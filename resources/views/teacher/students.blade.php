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
        @foreach ($assignments->pluck('section')->filter()->unique('id') as $section)
          <option value="{{ $section->name }}">{{ $section->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="overflow-x-auto">
      <table class="sg-table" id="teacher-students-table">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th data-col="section">Section</th>
            <th data-col="subject">Subject</th>
            <th class="text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($students as $student)
            <tr>
              <td style="color:var(--sg-text)">{{ $student->student_id ?? '—' }}</td>
              <td class="font-medium" style="color:var(--sg-text)">{{ $student->name }}</td>
              <td data-col="section" style="color:var(--sg-text-muted)">
                @foreach ($assignments->pluck('section')->filter()->unique('id') as $section)
                  @if($section->enrollments()->where('student_id', $student->id)->exists())
                    {{ $section->name }}
                    @break
                  @endif
                @endforeach
              </td>
              <td data-col="subject" style="color:var(--sg-text-muted)">
                @foreach ($assignments as $assignment)
                  @if($assignment->section->enrollments()->where('student_id', $student->id)->exists())
                    {{ $assignment->subject->code ?? '' }}
                    @break
                  @endif
                @endforeach
              </td>
              <td class="text-right">
                <button class="sg-btn sg-btn-ghost !p-2" data-toast="Viewing grades for {{ $student->name }} (demo).">
                  <i data-lucide="external-link" class="w-4 h-4"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-10" style="color:var(--sg-text-muted)">No students found in your classes.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div data-empty-state="teacher-students-table" class="hidden text-center py-12">
        <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
        <p class="text-sm" style="color:var(--sg-text-muted)">No students match your search.</p>
      </div>
    </div>
  </div>
</div>
@endsection
