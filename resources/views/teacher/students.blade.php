@extends('layouts.app', ['role' => 'teacher', 'active' => 'students', 'title' => 'Students'])

@section('content')
  <div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Students in My Classes</h2>
        <p class="text-sm" style="color:var(--sg-text-muted)">All students across your assigned sections</p>
      </div>
      <button data-modal-open="add-student-modal" class="sg-btn sg-btn-primary">
        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>Add Student
      </button>
    </div>

    <div class="sg-card p-5">
      <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="relative flex-1">
          <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2"
            style="color:var(--sg-text-muted)"></i>
          <input type="text" data-table-search="teacher-students-table" placeholder="Search students…"
            class="sg-input pl-9">
        </div>
        <select data-table-filter="teacher-students-table" data-filter-column="course" class="sg-select sm:w-44">
          <option value="">All Courses</option>
          @foreach ($courseOptions as $course)
            <option value="{{ $course }}">{{ $course }}</option>
          @endforeach
        </select>
        <select data-table-filter="teacher-students-table" data-filter-column="subject" class="sg-select sm:w-44">
          <option value="">All Subjects</option>
          @foreach ($subjectOptions as $code)
            <option value="{{ $code }}">{{ $code }}</option>
          @endforeach
        </select>
        <select data-table-filter="teacher-students-table" data-filter-column="section" class="sg-select sm:w-44">
          <option value="">All Sections</option>
          @foreach ($assignments->pluck('section')->filter()->unique('id') as $section)
            <option value="{{ $section->name }}">{{ $section->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="overflow-x-auto no-print">
        <table class="sg-table" id="teacher-students-table">
          <thead>
            <tr>
              <th>Student ID</th>
              <th>Name</th>
              <th data-col="course">Course</th>
              <th data-col="section">Section</th>
              <th data-col="subject">Subject Code</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($students as $student)
              <tr>
                <td style="color:var(--sg-text)">{{ $student->student_id ?? '—' }}</td>
                <td class="font-medium" style="color:var(--sg-text)">{{ $student->last_name_first }}</td>
                <td data-col="course" style="color:var(--sg-text-muted)">
                  {{ $student->short_course_name ?? '—' }}
                </td>
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
                  <a href="{{ route('teacher.grades') }}?student={{ $student->id }}" class="sg-btn sg-btn-ghost !p-2"
                    title="View grades for {{ $student->last_name_first }}">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-10" style="color:var(--sg-text-muted)">No students found in your
                  classes.</td>
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

  {{-- Add Student Modal --}}
  <div id="add-student-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
    <div class="sg-modal sg-surface rounded-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
      <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0"
        style="border-color:var(--sg-border)">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:var(--sg-info-bg)">
            <i data-lucide="user-plus" class="w-4.5 h-4.5" style="color:var(--sg-navy-600)"></i>
          </div>
          <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Add New Student</h3>
        </div>
        <button data-modal-close="add-student-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x"
            class="w-4 h-4"></i></button>
      </div>
      <form id="add-student-form" method="POST" action="{{ route('teacher.students.store') }}"
        class="p-6 overflow-y-auto space-y-4">
        @csrf
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">First Name</label>
            <input type="text" name="firstname" required placeholder="e.g. Juan" class="sg-input">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Last Name</label>
            <input type="text" name="lastname" required placeholder="e.g. Dela Cruz" class="sg-input">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Student ID</label>
            <input type="text" name="student_id" required placeholder="e.g. 2026-0001" class="sg-input">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email</label>
            <input type="email" name="email" required placeholder="e.g. student@ckcgingoog.edu.ph" class="sg-input">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Password</label>
            <input type="password" name="password" required minlength="8" class="sg-input">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Confirm Password</label>
            <input type="password" name="password_confirmation" required minlength="8" class="sg-input">
          </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t" style="border-color:var(--sg-border)">
          <button type="button" data-modal-close="add-student-modal" class="sg-btn sg-btn-outline">Cancel</button>
          <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4 mr-2"></i>Save
            Student</button>
        </div>
      </form>
    </div>
  </div>
@endsection