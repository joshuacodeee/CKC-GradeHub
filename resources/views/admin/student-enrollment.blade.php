@extends('layouts.app', ['role' => 'admin', 'active' => 'students', 'title' => 'Manage Enrollment'])

@section('content')
  @php
    $syName = $schoolYear?->name ?? '—';
    $semester = $schoolYear?->semester ?? '—';
  @endphp

  <div class="space-y-5">

    {{-- Back link + title --}}
    <div class="flex items-center gap-3">
      <a href="{{ route('admin.students') }}" class="sg-btn sg-btn-ghost !p-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
      </a>
      <div>
        <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">
          Manage Enrollment
        </h2>
        <p class="text-sm" style="color:var(--sg-text-muted)">
          {{ $student->last_name_first }} · {{ $student->student_id }}
        </p>
      </div>
    </div>

    {{-- Profile + status card --}}
    <div class="sg-card p-5">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <p class="text-xs uppercase tracking-wider" style="color:var(--sg-text-muted)">Course</p>
          <p class="font-medium" style="color:var(--sg-text)">{{ $student->course ?? '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wider" style="color:var(--sg-text-muted)">Year / Block</p>
          <p class="font-medium" style="color:var(--sg-text)">
            {{ $student->year_level ?? '—' }}{{ $student->block ? ' · ' . $student->block : '' }}
          </p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wider" style="color:var(--sg-text-muted)">Standing</p>
          <p class="font-medium flex items-center gap-2" style="color:var(--sg-text)">
            @if($student->is_irregular)
              <span class="sg-badge sg-badge-warning">Irregular</span>
            @else
              <span class="sg-badge sg-badge-success">Regular</span>
            @endif
          </p>
        </div>
      </div>

      <div class="mt-4 flex flex-wrap gap-2 pt-4 border-t" style="border-color:var(--sg-border)">
        <form method="POST" action="{{ route('admin.student-enrollment.toggle-irregular', $student) }}">
          @csrf
          <button type="submit" class="sg-btn sg-btn-outline">
            <i data-lucide="repeat" class="w-4 h-4"></i>
            {{ $student->is_irregular ? 'Mark as Regular' : 'Mark as Irregular' }}
          </button>
        </form>
        <form method="POST" action="{{ route('admin.student-enrollment.sync', $student) }}">
          @csrf
          <button type="submit" class="sg-btn sg-btn-outline" data-toast="Re-sync from curriculum">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i> Sync from curriculum
          </button>
        </form>
      </div>
    </div>

    {{-- Currently enrolled offerings --}}
    <div class="sg-card p-5">
      <div class="flex items-center justify-between mb-3">
        <div>
          <h3 class="font-display font-semibold" style="color:var(--sg-text)">Currently enrolled class offerings</h3>
          <p class="text-xs" style="color:var(--sg-text-muted)">School year {{ $syName }} · {{ $semester }}</p>
        </div>
      </div>
      @if($currentOfferings->isEmpty())
        <p class="text-sm py-6 text-center" style="color:var(--sg-text-muted)">
          No class offerings yet. Use the checklist below to assign this student to a class.
        </p>
      @else
        <div class="overflow-x-auto no-print">
          <table class="sg-table">
            <thead>
              <tr>
                <th>Subject</th>
                <th>Class Code</th>
                <th>Section / Year</th>
                <th>Teacher</th>
                <th>Assigned</th>
                <th class="text-right">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($currentOfferings as $offering)
                @php
                  $pivot = \App\Models\ClassOfferingStudent::where('teacher_subject_id', $offering->id)
                    ->where('student_id', $student->id)
                    ->first();
                @endphp
                <tr>
                  <td style="color:var(--sg-text)">
                    <span class="font-medium">{{ $offering->subject?->code }}</span>
                    <span class="text-xs ml-1" style="color:var(--sg-text-muted)">{{ $offering->subject?->name }}</span>
                  </td>
                  <td><span class="sg-badge sg-badge-info">{{ $offering->class_code ?? '—' }}</span></td>
                  <td style="color:var(--sg-text-muted)">
                    {{ $offering->section?->name ?? '—' }}
                    <span class="text-xs ml-1">{{ $offering->section?->year_level }}</span>
                  </td>
                  <td style="color:var(--sg-text-muted)">{{ $offering->teacher?->name ?? '—' }}</td>
                  <td style="color:var(--sg-text-muted)">
                    {{ $pivot?->assigned_by ? 'By admin' : 'Auto' }}
                  </td>
                  <td class="text-right">
                    <form method="POST" action="{{ route('admin.student-enrollment.unassign', [$student, $offering]) }}"
                      onsubmit="return confirm('Remove this student from {{ $offering->subject?->code }} · {{ $offering->section?->name }}?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="sg-btn sg-btn-ghost !p-2" style="color:var(--sg-danger)" title="Remove">
                        <i data-lucide="x" class="w-4 h-4"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    {{-- Curriculum checklist --}}
    <div class="sg-card p-5">
      <div class="flex items-center justify-between mb-3">
        <div>
          <h3 class="font-display font-semibold" style="color:var(--sg-text)">Curriculum checklist</h3>
          <p class="text-xs" style="color:var(--sg-text-muted)">
            {{ $curriculum?->course ?? $student->course }}
            · {{ $curriculum?->year_level ?? $student->year_level }}
            · {{ $curriculum?->semester ?? $semester }}
          </p>
        </div>
        @if(!$curriculum)
          <span class="sg-badge sg-badge-brass">No curriculum defined</span>
        @endif
      </div>

      @if($curriculum && count($checklist) > 0)
        <div class="space-y-2">
          @foreach ($checklist as $item)
            @php
              $status = $item['status'];
              $icon = $status === 'passed' ? 'check-circle-2' : ($status === 'enrolled' ? 'dot' : 'alert-circle');
              $tone = $status === 'passed' ? 'success' : ($status === 'enrolled' ? 'info' : 'danger');
              $label = $status === 'passed' ? 'Passed' : ($status === 'enrolled' ? 'Enrolled' : 'Missing');
            @endphp
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg border" style="border-color:var(--sg-border)">
              <i data-lucide="{{ $icon }}" class="w-5 h-5 flex-shrink-0"
                style="color:var(--sg-{{ $tone === 'danger' ? 'danger' : ($tone === 'success' ? 'success' : 'navy-600') }})"></i>
              <div class="flex-1 min-w-0">
                <p class="font-medium text-sm" style="color:var(--sg-text)">
                  {{ $item['subject']->code }} — {{ $item['subject']->name }}
                </p>
                <p class="text-xs" style="color:var(--sg-text-muted)">{{ $item['note'] }}</p>
              </div>
              <span class="sg-badge sg-badge-{{ $tone }}">{{ $label }}</span>
              @if($status === 'missing')
                <button type="button" class="sg-btn sg-btn-primary" data-assign-subject="{{ $item['subject']->id }}"
                  data-subject-label="{{ $item['subject']->code }} — {{ $item['subject']->name }}">
                  <i data-lucide="user-plus" class="w-4 h-4"></i> Assign
                </button>
              @elseif($status === 'enrolled' && $item['offering'])
                <span class="text-xs" style="color:var(--sg-text-muted)">
                  {{ $item['offering']->class_code }}
                </span>
              @endif
            </div>
          @endforeach
        </div>
      @elseif(!$curriculum)
        <p class="text-sm py-6 text-center" style="color:var(--sg-text-muted)">
          No curriculum is defined for {{ $student->course }} · {{ $student->year_level }} · {{ $semester }}. Create one in
          <a href="{{ route('admin.curricula.create') }}" class="underline">Curricula</a> to enable auto-enrollment and the
          missing-subjects checklist.
        </p>
      @else
        <p class="text-sm py-6 text-center" style="color:var(--sg-text-muted)">No subjects in this curriculum.</p>
      @endif
    </div>
  </div>

  {{-- Assign modal --}}
  <div id="assign-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
    <div class="sg-modal sg-surface rounded-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
      <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0"
        style="border-color:var(--sg-border)">
        <div>
          <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Assign to a class</h3>
          <p class="text-xs" style="color:var(--sg-text-muted)" data-assign-subject-label></p>
        </div>
        <button data-modal-close="assign-modal" class="sg-btn sg-btn-ghost !p-2">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <form id="assign-form" method="POST" action="{{ route('admin.student-enrollment.assign', $student) }}"
        class="p-6 space-y-4">
        @csrf
        <input type="hidden" name="teacher_subject_id" id="assign-teacher-subject-id" value="">
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Class offerings for this
            subject</label>
          <div id="offerings-list" class="space-y-2 max-h-80 overflow-y-auto">
            <p class="text-sm text-center py-4" style="color:var(--sg-text-muted)">Loading…</p>
          </div>
          <p id="offerings-empty" class="hidden text-sm text-center py-4" style="color:var(--sg-text-muted)">
            No active class offerings found for this subject in the current school year.
          </p>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t" style="border-color:var(--sg-border)">
          <button type="button" data-modal-close="assign-modal" class="sg-btn sg-btn-outline">Cancel</button>
          <button type="submit" class="sg-btn sg-btn-primary" id="assign-submit" disabled>
            <i data-lucide="check" class="w-4 h-4"></i> Assign
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const offeringList = document.getElementById('offerings-list');
    const offeringEmpty = document.getElementById('offerings-empty');
    const assignInput = document.getElementById('assign-teacher-subject-id');
    const assignSubmit = document.getElementById('assign-submit');
    const assignLabel = document.querySelector('[data-assign-subject-label]');

    function openAssignModal(subjectId, subjectLabel) {
      assignLabel.textContent = subjectLabel;
      assignInput.value = '';
      assignSubmit.disabled = true;
      offeringList.innerHTML = '<p class="text-sm text-center py-4" style="color:var(--sg-text-muted)">Loading…</p>';
      offeringEmpty.classList.add('hidden');
      document.getElementById('assign-modal').classList.remove('hidden');

      fetch('{{ route('admin.student-enrollment.offerings', $student) }}?subject_id=' + subjectId, {
        headers: { 'Accept': 'application/json' }
      })
        .then((r) => r.json())
        .then((data) => {
          offeringList.innerHTML = '';
          if (!data.offerings || data.offerings.length === 0) {
            offeringEmpty.classList.remove('hidden');
            return;
          }
          data.offerings.forEach((o) => {
            const id = 'offering-' + o.id;
            const row = document.createElement('label');
            row.className = 'flex items-center gap-3 px-3 py-2.5 rounded-lg border cursor-pointer hover:bg-slate-50';
            row.style.borderColor = 'var(--sg-border)';
            row.innerHTML = `
              <input type="radio" name="_offering_pick" value="${o.id}" id="${id}" class="offering-radio">
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium" style="color:var(--sg-text)">${o.section_name ?? '—'} <span class="text-xs ml-1" style="color:var(--sg-text-muted)">${o.year_level ?? ''}${o.block ? ' · Block ' + o.block : ''}</span></p>
                <p class="text-xs" style="color:var(--sg-text-muted)">${o.teacher_name ?? '—'}</p>
              </div>
              <span class="sg-badge sg-badge-info">${o.class_code ?? '—'}</span>
            `;
            offeringList.appendChild(row);
          });
          document.querySelectorAll('.offering-radio').forEach((r) => {
            r.addEventListener('change', () => {
              assignInput.value = r.value;
              assignSubmit.disabled = false;
            });
          });
        })
        .catch(() => {
          offeringList.innerHTML = '<p class="text-sm text-center py-4" style="color:var(--sg-danger)">Failed to load offerings.</p>';
        });
    }

    document.querySelectorAll('[data-assign-subject]').forEach((btn) => {
      btn.addEventListener('click', () => {
        openAssignModal(btn.dataset.assignSubject, btn.dataset.subjectLabel);
      });
    });
  </script>
@endsection