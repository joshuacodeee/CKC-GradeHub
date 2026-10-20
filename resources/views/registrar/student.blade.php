@extends('layouts.app', ['role' => 'registrar', 'active' => 'students', 'title' => 'Student Academic Record'])

@section('content')
  <div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <a href="{{ route('registrar.students') }}" class="text-sm inline-flex items-center gap-1 mb-2" style="color:var(--sg-navy-600)">
          <i data-lucide="arrow-left" class="w-4 h-4"></i> Student records
        </a>
        <h1 class="font-display font-semibold text-xl" style="color:var(--sg-text)">{{ $student->last_name_first_middle }}</h1>
        <p class="text-sm" style="color:var(--sg-text-muted)">{{ $student->student_id ?? 'No student ID' }} · {{ $student->email }}</p>
      </div>
      <a href="{{ route('registrar.students.report', ['student' => $student, ...request()->only(['school_year_id', 'semester'])]) }}"
        class="sg-btn sg-btn-primary" target="_blank" rel="noopener">
        <i data-lucide="printer" class="w-4 h-4"></i> Print grade report
      </a>
    </div>

    <section class="sg-card p-5">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div><p class="text-xs uppercase tracking-wider" style="color:var(--sg-text-muted)">Course</p><p class="font-medium">{{ $student->course ?? '—' }}</p></div>
        <div><p class="text-xs uppercase tracking-wider" style="color:var(--sg-text-muted)">Year / block</p><p class="font-medium">{{ $student->year_level ?? '—' }}{{ $student->block ? ' · ' . $student->block : '' }}</p></div>
        <div><p class="text-xs uppercase tracking-wider" style="color:var(--sg-text-muted)">Student status</p><p class="font-medium">{{ $student->is_active ? 'Active' : 'Inactive' }}</p></div>
        <div>
          <p class="text-xs uppercase tracking-wider mb-1" style="color:var(--sg-text-muted)">Standing</p>
          <span class="sg-badge {{ $student->is_irregular ? 'sg-badge-warning' : 'sg-badge-info' }}">{{ $student->is_irregular ? 'Irregular' : 'Regular' }}</span>
        </div>
      </div>
      <div class="mt-4 pt-4 border-t" style="border-color:var(--sg-border)">
        <form method="POST" action="{{ route('registrar.students.irregular', $student) }}">
          @csrf
          @method('PATCH')
          <button type="submit" class="sg-btn sg-btn-outline">
            <i data-lucide="repeat" class="w-4 h-4"></i>
            {{ $student->is_irregular ? 'Mark as regular' : 'Manage as irregular' }}
          </button>
        </form>
      </div>
    </section>

    @if ($student->is_irregular)
      <section class="sg-card p-5">
        <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Assign an individual subject</h2>
        <p class="text-sm mt-1 mb-4" style="color:var(--sg-text-muted)">Choose a subject, school year, semester, and section. The teacher assignment is kept separate from the student's enrollment.</p>
        <form method="POST" action="{{ route('registrar.students.subjects.assign', $student) }}" class="space-y-4">
          @csrf
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
              <label for="assign-subject" class="block text-sm font-medium mb-1">Subject</label>
              <select id="assign-subject" name="subject_id" class="sg-select" required>
                <option value="">Select subject</option>
                @foreach ($subjects as $subject)
                  <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->code }} · {{ $subject->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label for="assign-school-year" class="block text-sm font-medium mb-1">School year</label>
              <select id="assign-school-year" name="school_year_id" class="sg-select" required>
                <option value="">Select school year</option>
                @foreach ($schoolYears as $schoolYear)
                  <option value="{{ $schoolYear->id }}" @selected(old('school_year_id') == $schoolYear->id)>{{ $schoolYear->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label for="assign-semester" class="block text-sm font-medium mb-1">Semester</label>
              <select id="assign-semester" name="semester" class="sg-select" required>
                <option value="">Select semester</option>
                @foreach (['First Semester', 'Second Semester', 'Summer'] as $semester)
                  <option value="{{ $semester }}" @selected(old('semester') === $semester)>{{ $semester }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label for="assign-section" class="block text-sm font-medium mb-1">Section</label>
              <select id="assign-section" name="section_id" class="sg-select" required disabled>
                <option value="">Select section</option>
              </select>
            </div>
          </div>
          <div>
            <label for="assign-offering" class="block text-sm font-medium mb-1">Teacher / class offering</label>
            <select id="assign-offering" name="teacher_subject_id" class="sg-select" required disabled>
              <option value="">Select a subject, period, and section first</option>
              @foreach ($offerings as $offering)
                @if ($offering->subject && $offering->section && $offering->schoolYear)
                  <option value="{{ $offering->id }}"
                    data-subject-id="{{ $offering->subject_id }}"
                    data-school-year-id="{{ $offering->school_year_id }}"
                    data-semester="{{ $offering->semester }}"
                    data-section-id="{{ $offering->section_id }}"
                    @selected(old('teacher_subject_id') == $offering->id)>
                    {{ $offering->section->name }} · {{ $offering->section->course }} · {{ $offering->teacher?->full_name ?? 'No teacher' }} · {{ $offering->class_code ?? 'No class code' }}
                  </option>
                @endif
              @endforeach
            </select>
          </div>
          @error('teacher_subject_id')<p class="text-sm" style="color:var(--sg-danger)">{{ $message }}</p>@enderror
          <div class="flex justify-end">
            <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="user-plus" class="w-4 h-4"></i> Assign subject</button>
          </div>
        </form>
      </section>
    @endif

    <section class="sg-card p-5">
      <div class="flex flex-wrap items-end justify-between gap-3 mb-4">
        <div>
          <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Academic history</h2>
          <p class="text-sm" style="color:var(--sg-text-muted)">Submitted grades and draft records from every available academic period.</p>
        </div>
        <form method="GET" action="{{ route('registrar.students.show', $student) }}" class="flex flex-wrap gap-2">
          <select name="school_year_id" class="sg-select w-auto" aria-label="Filter by school year">
            <option value="">All school years</option>
            @foreach ($schoolYears as $schoolYear)
              <option value="{{ $schoolYear->id }}" @selected((string) ($filters['school_year_id'] ?? '') === (string) $schoolYear->id)>{{ $schoolYear->name }}</option>
            @endforeach
          </select>
          <select name="semester" class="sg-select w-auto" aria-label="Filter by semester">
            <option value="">All semesters</option>
            @foreach (['First Semester', 'Second Semester', 'Summer'] as $semester)
              <option value="{{ $semester }}" @selected(($filters['semester'] ?? '') === $semester)>{{ $semester }}</option>
            @endforeach
          </select>
          <button type="submit" class="sg-btn sg-btn-outline">Filter</button>
        </form>
      </div>
      <div class="overflow-x-auto">
        <table class="sg-table">
          <thead><tr><th>Academic period</th><th>Subject</th><th>Teacher</th><th>Section</th><th>Prelim</th><th>Midterm</th><th>Final</th><th>Final grade</th><th>Remarks / status</th></tr></thead>
          <tbody>
            @forelse ($grades as $grade)
              @php
                $assignment = $grade->teacherAssignment;
                $academicYear = $assignment?->schoolYear ?? $grade->enrollment?->schoolYear;
                $subject = $assignment?->subject ?? $grade->subject;
                $teacher = $assignment?->teacher ?? $grade->teacher;
                $section = $assignment?->section ?? $grade->enrollment?->section;
                $semester = $assignment?->semester ?? $academicYear?->semester;
              @endphp
              <tr>
                <td>{{ $academicYear?->name ?? '—' }} · {{ $semester ?? '—' }}</td>
                <td>{{ $subject?->code ?? '—' }} · {{ $subject?->name ?? 'Unknown subject' }}</td>
                <td>{{ $teacher?->full_name ?? '—' }}</td>
                <td>{{ $section?->name ?? '—' }}</td>
                <td>{{ $grade->prelim ?? '—' }}</td><td>{{ $grade->midterm ?? '—' }}</td><td>{{ $grade->final ?? '—' }}</td>
                <td>{{ $grade->final_grade ?? '—' }}</td>
                <td>
                  @if ($grade->status === 'submitted')
                    <span class="sg-badge {{ $grade->remarks === 'Passed' ? 'sg-badge-success' : 'sg-badge-danger' }}">{{ $grade->remarks ?? 'Submitted' }}</span>
                  @else
                    <span class="sg-badge sg-badge-warning">Draft</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="9" class="text-center py-8" style="color:var(--sg-text-muted)">No grade records for this academic period.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <section class="sg-card p-5">
      <h2 class="font-display font-semibold text-lg mb-1" style="color:var(--sg-text)">Subject enrollment history</h2>
      <p class="text-sm mb-4" style="color:var(--sg-text-muted)">Individual class offerings, including passed, failed, and currently enrolled subjects.</p>
      <div class="overflow-x-auto">
        <table class="sg-table">
          <thead><tr><th>Academic period</th><th>Subject</th><th>Section</th><th>Teacher</th><th>Enrollment status</th></tr></thead>
          <tbody>
            @forelse ($memberships as $membership)
              @php
                $offering = $membership->teacherSubject;
                $record = $membership->gradeRecord;
                $term = $offering->schoolYear;
                $enrollmentStatus = $record?->status === 'submitted'
                  ? ($record->remarks ?? 'Submitted')
                  : ($offering->status === 'active' ? 'Currently enrolled' : 'Historical enrollment');
              @endphp
              <tr>
                <td>{{ $term?->name ?? '—' }} · {{ $offering->semester }}</td>
                <td>{{ $offering->subject?->code ?? '—' }} · {{ $offering->subject?->name ?? 'Unknown subject' }}</td>
                <td>{{ $offering->section?->name ?? '—' }}</td>
                <td>{{ $offering->teacher?->full_name ?? '—' }}</td>
                <td><span class="sg-badge {{ $enrollmentStatus === 'Passed' ? 'sg-badge-success' : ($enrollmentStatus === 'Failed' ? 'sg-badge-danger' : 'sg-badge-info') }}">{{ $enrollmentStatus }}</span></td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center py-8" style="color:var(--sg-text-muted)">No subject enrollment records for this academic period.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    @if ($signatureUpdatedAt)
      <p class="text-xs text-right" style="color:var(--sg-text-muted)">
        Signature settings last updated {{ \Illuminate\Support\Carbon::parse($signatureUpdatedAt)->format('M j, Y g:i A') }}
        @if ($updatedBy) by {{ $updatedBy->full_name }} @endif
      </p>
    @endif
  </div>

  @push('scripts')
    <script>
      (() => {
        const subject = document.getElementById('assign-subject');
        const year = document.getElementById('assign-school-year');
        const semester = document.getElementById('assign-semester');
        const section = document.getElementById('assign-section');
        const offering = document.getElementById('assign-offering');
        if (!subject || !year || !semester || !section || !offering) return;

        const sourceOptions = [...offering.options].slice(1);
        const refreshOfferings = () => {
          const matches = sourceOptions.filter((option) =>
            option.dataset.subjectId === subject.value &&
            option.dataset.schoolYearId === year.value &&
            option.dataset.semester === semester.value
          );
          const sections = new Map(matches.map((option) => [option.dataset.sectionId, option.textContent.split(' · ')[0]]));
          const selectedSection = section.value;
          section.replaceChildren(new Option('Select section', ''));
          sections.forEach((label, id) => section.add(new Option(label, id)));
          if (sections.has(selectedSection)) section.value = selectedSection;
          section.disabled = sections.size === 0;

          offering.replaceChildren(new Option(
            matches.length ? 'Select teacher / class offering' : 'No offerings available for this period',
            ''
          ));
          matches.filter((option) => !section.value || option.dataset.sectionId === section.value)
            .forEach((option) => offering.add(new Option(option.textContent.trim(), option.value)));
          offering.disabled = offering.options.length < 2;
        };

        [subject, year, semester].forEach((input) => input.addEventListener('change', refreshOfferings));
        section.addEventListener('change', refreshOfferings);
        refreshOfferings();
      })();
    </script>
  @endpush
@endsection
