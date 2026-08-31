@extends('layouts.app', ['role' => 'admin', 'active' => 'subjects', 'title' => 'Subject Details'])

@section('content')
  <div class="space-y-5">
    <div class="flex items-start justify-between flex-wrap gap-3">
      <div>
        <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">
          {{ $subject->code }} — {{ $subject->name }}
        </h2>
        <p class="text-sm" style="color:var(--sg-text-muted)">
          {{ $subject->units }} unit{{ $subject->units != 1 ? 's' : '' }}
        </p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('admin.subjects.edit', $subject) }}" class="sg-btn sg-btn-outline"><i data-lucide="pencil" class="w-4 h-4"></i> Edit</a>
        <a href="{{ route('admin.subjects') }}" class="sg-btn sg-btn-ghost"><i data-lucide="arrow-left" class="w-4 h-4"></i> Back</a>
      </div>
    </div>

    <div class="sg-card p-5">
      <h3 class="font-display font-semibold mb-2" style="color:var(--sg-text)">Description</h3>
      <p class="text-sm" style="color:var(--sg-text-muted)">{{ $subject->description ?: 'No description provided.' }}</p>
    </div>

    <div class="sg-card p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-display font-semibold" style="color:var(--sg-text)">Teacher Assignments</h3>
        <a href="{{ route('admin.assignments.create') }}" class="sg-btn sg-btn-primary !py-1.5 !text-xs">
          <i data-lucide="plus" class="w-4 h-4"></i> Assign Teacher
        </a>
      </div>
      <div class="overflow-x-auto">
        <table class="sg-table">
          <thead>
            <tr>
              <th>Teacher</th>
              <th>Section</th>
              <th>School Year</th>
              <th>Semester</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($subject->teacherAssignments as $a)
              <tr>
                <td style="color:var(--sg-text)">{{ $a->teacher?->name ?? '—' }}</td>
                <td style="color:var(--sg-text-muted)">{{ $a->section?->name ?? '—' }}</td>
                <td style="color:var(--sg-text-muted)">{{ $a->schoolYear?->name ?? '—' }}</td>
                <td style="color:var(--sg-text-muted)">{{ $a->semester ?? '—' }}</td>
                <td>
                  <span class="sg-badge {{ $a->status === 'active' ? 'sg-badge-success' : 'sg-badge-info' }}">{{ ucfirst($a->status) }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-10" style="color:var(--sg-text-muted)">No teachers assigned to this subject yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
