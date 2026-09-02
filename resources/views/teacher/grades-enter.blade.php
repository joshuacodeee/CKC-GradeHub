@extends('layouts.app', ['role' => 'teacher', 'active' => 'grades', 'title' => 'Enter Grades'])

@section('content')
  <div class="space-y-5">

    {{-- Class info bar --}}
    <div class="sg-card p-5 flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">
          {{ $teacherSubject->subject->code ?? '—' }} — {{ $teacherSubject->subject->name ?? 'Unnamed Subject' }}
        </h2>
        <p class="text-sm" style="color:var(--sg-text-muted)">
          {{ $teacherSubject->section->name ?? '—' }} · {{ $teacherSubject->section->course ?? '' }}
          {{ $teacherSubject->section->year_level ?? '' }}
        </p>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <span class="sg-badge sg-badge-warning">Final = (Prelim + Midterm + Final) / 3</span>
        <span class="sg-badge sg-badge-info">{{ $enrollments->count() }} Students</span>
        <a href="{{ route('teacher.reports.show', $teacherSubject) }}" class="sg-btn sg-btn-outline !text-xs !px-2 !py-1">
          <i data-lucide="file-bar-chart-2" class="w-3.5 h-3.5"></i> View Report
        </a>
      </div>
    </div>

    {{-- Grade entry form --}}
    <form id="grade-entry-form" method="POST" action="{{ route('teacher.grades.save', $teacherSubject) }}"
      class="sg-card p-5">
      @csrf
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-display font-semibold" style="color:var(--sg-text)">Grade Sheet</h3>
        <span class="text-xs" style="color:var(--sg-text-muted)">Final grade auto-calculates as you type</span>
      </div>

      <div class="overflow-x-auto no-print">
        <table class="sg-table">
          <thead>
            <tr>
              <th>Student ID</th>
              <th>Student Name</th>
              <th class="text-center">Prelim</th>
              <th class="text-center">Midterm</th>
              <th class="text-center">Final</th>
              <th class="text-center">Final Grade</th>
              <th class="text-center">Equivalent</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($enrollments as $enrollment)
              @php
                // $existingGrades is keyed by student_id (user id) per the controller.
                $existing = $existingGrades[$enrollment->student_id] ?? null;
                $existingEq = $existing && $existing->final_grade !== null
                  ? number_format(\App\Support\GradingScale::equivalent((float) $existing->final_grade), 1)
                  : null;
              @endphp
              <tr data-grade-row>
                <td style="color:var(--sg-text)">{{ $enrollment->student->student_id ?? '—' }}</td>
                <td class="font-medium" style="color:var(--sg-text)">{{ $enrollment->student->name ?? '—' }}</td>
                <input type="hidden" name="grades[{{ $loop->index }}][enrollment_id]" value="{{ $enrollment->id }}">
                <input type="hidden" name="grades[{{ $loop->index }}][student_id]" value="{{ $enrollment->student_id }}">
                <td class="text-center">
                  <input type="number" name="grades[{{ $loop->index }}][prelim]" min="0" max="100" step="0.01"
                    value="{{ $existing->prelim ?? '' }}" class="sg-input grade-input !w-20 mx-auto !text-center"
                    data-subject-id="{{ $teacherSubject->subject_id }}" data-enrollment-id="{{ $enrollment->id }}">
                </td>
                <td class="text-center">
                  <input type="number" name="grades[{{ $loop->index }}][midterm]" min="0" max="100" step="0.01"
                    value="{{ $existing->midterm ?? '' }}" class="sg-input grade-input !w-20 mx-auto !text-center">
                </td>
                <td class="text-center">
                  <input type="number" name="grades[{{ $loop->index }}][final]" min="0" max="100" step="0.01"
                    value="{{ $existing->final ?? '' }}" class="sg-input grade-input !w-20 mx-auto !text-center">
                </td>
                <td class="text-center font-medium" data-final-grade>
                  {{ $existing && $existing->final_grade !== null ? number_format($existing->final_grade, 2) : '—' }}
                </td>
                <td class="text-center font-semibold" data-final-equivalent style="color:var(--sg-navy-700)">
                  {{ $existingEq ?? '—' }}
                </td>
                <td data-remarks>
                  @if($existing && $existing->remarks)
                    <span
                      class="sg-badge {{ $existing->remarks === 'Passed' ? 'sg-badge-success' : 'sg-badge-danger' }}">{{ $existing->remarks }}</span>
                  @else
                    <span class="sg-badge sg-badge-info">Pending</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="flex flex-wrap items-center justify-end gap-2 mt-5 pt-5 border-t no-print"
        style="border-color:var(--sg-border)">
        <button type="button" id="btn-print" class="sg-btn sg-btn-outline"><i data-lucide="printer" class="w-4 h-4"></i>
          Print</button>
        <button type="submit" name="action" value="draft" class="sg-btn sg-btn-outline"><i data-lucide="save"
            class="w-4 h-4"></i> Save Draft</button>
        <button type="submit" name="action" value="submit" class="sg-btn sg-btn-primary"
          data-confirm="Submit all grades for this class?" data-confirm-title="Submit Grades">
          <i data-lucide="send" class="w-4 h-4"></i> Submit Grades
        </button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
  <script>
    // Auto-fill final_grade, equivalent, and remarks cells in real-time.
    // Spec §30: Final Grade = (Prelim + Midterm + Final) / 3, passing is >= 75.
    // The Equivalent column shows the CKC 1.0–5.0 grade so the teacher can
    // confirm at a glance how the raw 0-100 input maps to the printed value.
    document.addEventListener('DOMContentLoaded', function () {
      function equivalentFor(pct) {
        if (pct === null || isNaN(pct)) return null;
        if (pct < 0) return 5.0;
        if (pct < 75) return 5.0;
        if (pct >= 95) return 1.0;
        const diff = 94 - Math.round(pct);
        if (diff < 0) return 1.0;
        return Math.min(1.1 + (diff * 0.1), 3.0);
      }

      function computeFinalGrade(prelim, midterm, final) {
        const p = parseFloat(prelim), m = parseFloat(midterm), f = parseFloat(final);
        const values = [p, m, f].filter(n => !isNaN(n));
        if (values.length === 0) return null;
        const sum = values.reduce((a, b) => a + b, 0);
        const grade = Math.round((sum / values.length) * 100) / 100;
        const passed = grade >= 75;
        return {
          grade: grade.toFixed(2),
          passed,
          remarks: passed ? 'Passed' : 'Failed',
          equivalent: equivalentFor(grade).toFixed(1),
        };
      }

      document.querySelectorAll('[data-grade-row]').forEach(function (row) {
        const inputs = row.querySelectorAll('.grade-input');
        const finalCell = row.querySelector('[data-final-grade]');
        const equivCell = row.querySelector('[data-final-equivalent]');
        const remarksCell = row.querySelector('[data-remarks]');

        function recalc() {
          const prelim = row.querySelector('[name*="[prelim]"]').value;
          const midterm = row.querySelector('[name*="[midterm]"]').value;
          const final = row.querySelector('[name*="[final]"]').value;
          const result = computeFinalGrade(prelim, midterm, final);
          if (!result) {
            if (finalCell) finalCell.textContent = '—';
            if (equivCell) equivCell.textContent = '—';
            if (remarksCell) remarksCell.innerHTML = '<span class="sg-badge sg-badge-info">Pending</span>';
            return;
          }
          if (finalCell) finalCell.textContent = result.grade;
          if (equivCell) equivCell.textContent = result.equivalent;
          if (remarksCell) {
            remarksCell.innerHTML = result.passed
              ? '<span class="sg-badge sg-badge-success">Passed</span>'
              : '<span class="sg-badge sg-badge-danger">Failed</span>';
          }
        }

        inputs.forEach(function (input) {
          input.addEventListener('input', recalc);
        });
        recalc();
      });

      // Initialize icons
      if (window.lucide) window.lucide.createIcons();

      // Print button
      document.getElementById('btn-print')?.addEventListener('click', function () {
        window.print();
      });
    });
  </script>
@endpush