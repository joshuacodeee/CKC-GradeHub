@extends('layouts.app', ['role' => 'admin', 'active' => 'curricula', 'title' => 'Curricula'])

@section('content')
  <div class="space-y-5">

    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Curricula</h2>
        <p class="text-sm" style="color:var(--sg-text-muted)">
          Define the subjects required for each (course, year, semester). Used to auto-enroll regular students and to
          detect what irregular students still need.
        </p>
      </div>
      <a href="{{ route('admin.curricula.create') }}" class="sg-btn sg-btn-primary">
        <i data-lucide="plus" class="w-4 h-4"></i> New Curriculum
      </a>
    </div>

    <div class="sg-card p-5">
      <div class="overflow-x-auto no-print">
        <table class="sg-table">
          <thead>
            <tr>
              <th>Course</th>
              <th>Year Level</th>
              <th>Semester</th>
              <th>School Year</th>
              <th>Subjects</th>
              <th>Status</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($curricula as $c)
              <tr>
                <td class="font-medium" style="color:var(--sg-text)">{{ $c->course }}</td>
                <td style="color:var(--sg-text-muted)">{{ $c->year_level }}</td>
                <td style="color:var(--sg-text-muted)">{{ $c->semester }}</td>
                <td style="color:var(--sg-text-muted)">{{ $c->schoolYear?->name ?? 'All years' }}</td>
                <td style="color:var(--sg-text-muted)">
                  {{ $c->curriculumSubjects->count() }} subject{{ $c->curriculumSubjects->count() === 1 ? '' : 's' }}
                </td>
                <td>
                  @if($c->is_active)
                    <span class="sg-badge sg-badge-success">Active</span>
                  @else
                    <span class="sg-badge sg-badge-brass">Inactive</span>
                  @endif
                </td>
                <td class="text-right">
                  <a href="{{ route('admin.curricula.edit', $c) }}" class="sg-btn sg-btn-ghost !p-2" title="Edit">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                  </a>
                  <form method="POST" action="{{ route('admin.curricula.destroy', $c) }}" class="inline"
                    onsubmit="return confirm('Delete this curriculum? Existing enrollments will not be affected, but no new students will be auto-enrolled against it.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="sg-btn sg-btn-ghost !p-2" title="Delete" style="color:var(--sg-danger)">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-12" style="color:var(--sg-text-muted)">
                  No curricula defined yet. Without one, regular students fall back to "every active class offering in their
                  section."
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection