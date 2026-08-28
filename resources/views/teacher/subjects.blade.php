@extends('layouts.app', ['role' => 'teacher', 'active' => 'subjects', 'title' => 'My Subjects'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">My Subjects</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">Subjects assigned to you this semester</p>
  </div>

  <div class="sg-card p-5">
    <div class="overflow-x-auto">
      <table class="sg-table">
        <thead>
          <tr>
            <th>Code</th>
            <th>Subject Name</th>
            <th>Units</th>
            <th>Department</th>
            <th>Sections Handled</th>
            <th>Total Students</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($subjects as $subject)
            <tr>
              <td style="color:var(--sg-text)">{{ $subject->code }}</td>
              <td class="font-medium" style="color:var(--sg-text)">{{ $subject->name }}</td>
              <td style="color:var(--sg-text-muted)">{{ $subject->units }}</td>
              <td style="color:var(--sg-text-muted)">{{ $subject->department ?? '—' }}</td>
              <td style="color:var(--sg-text-muted)">
                @if (isset($subjectDetails[$subject->id]))
                  {{ $subjectDetails[$subject->id]->count() }} section(s)
                @else
                  —
                @endif
              </td>
              <td style="color:var(--sg-text-muted)">
                @if (isset($subjectDetails[$subject->id]))
                  {{ $subjectDetails[$subject->id]->sum(fn($d) => $d->section?->enrollments()->where('status','enrolled')->count() ?? 0) }}
                @else
                  —
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-10" style="color:var(--sg-text-muted)">No subjects assigned to you.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
