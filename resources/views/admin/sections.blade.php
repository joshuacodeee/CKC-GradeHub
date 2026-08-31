@extends('layouts.app', ['role' => 'admin', 'active' => 'sections', 'title' => 'Sections'])

@section('content')
<div class="space-y-5">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Sections</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">
        {{ $sections->where('is_active', true)->count() }} active sections
      </p>
    </div>
    <a href="{{ route('admin.sections.create') }}" class="sg-btn sg-btn-primary whitespace-nowrap">
      <i data-lucide="layers" class="w-4 h-4"></i> Add Section
    </a>
  </div>

  <div class="sg-card p-5">
    @include('components.table', [
      'tableId' => 'sections-table',
      'searchPlaceholder' => 'Search by section name, course, or year…',
      'filterColumn' => 'status',
      'filterOptions' => ['Active', 'Inactive'],
    ])

    <div class="overflow-x-auto -mx-5 px-5">
      <table class="sg-table" id="sections-table">
        <thead>
          <tr>
            <th class="cursor-pointer" data-sort-column>Section Name</th>
            <th class="cursor-pointer" data-sort-column>Course</th>
            <th class="cursor-pointer" data-sort-column>Year Level</th>
            <th class="cursor-pointer" data-sort-column>Block</th>
            <th class="cursor-pointer" data-sort-column># Students</th>
            <th class="cursor-pointer" data-sort-column>Status</th>
            <th class="text-right no-print">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($sections as $section)
            <tr>
              <td style="color:var(--sg-text)">
                <span data-col="status">{{ $section->name }}</span>
              </td>
              <td style="color:var(--sg-text-muted)">{{ $section->course }}</td>
              <td style="color:var(--sg-text-muted)">{{ $section->year_level }}</td>
              <td style="color:var(--sg-text-muted)">{{ $section->block ?: '—' }}</td>
              <td style="color:var(--sg-text-muted)">{{ $section->enrollments_count }}</td>
              <td>
                <span data-col="status">
                  @if($section->is_active)
                    <span class="sg-badge sg-badge-success">Active</span>
                  @else
                    <span class="sg-badge sg-badge-danger">Inactive</span>
                  @endif
                </span>
              </td>
              <td class="text-right no-print">
                <div class="flex justify-end gap-1">
                  <form method="POST" action="{{ route('admin.sections.sync', $section) }}"
                    data-confirm="Re-sync enrollments for {{ $section->name }}? Existing enrollments are kept; only missing students are added."
                    data-confirm-title="Sync Section">
                    @csrf
                    <button type="submit" class="sg-btn sg-btn-ghost !p-2" title="Sync enrollments"
                      style="color:var(--sg-navy-600)">
                      <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    </button>
                  </form>
                  <a href="{{ route('admin.sections.edit', $section) }}"
                    class="sg-btn sg-btn-ghost !p-2" title="Edit">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                  </a>
                  <form method="POST" action="{{ route('admin.sections.destroy', $section) }}"
                    data-confirm="Delete section {{ $section->name }}? This cannot be undone."
                    data-confirm-title="Delete Section">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="sg-btn sg-btn-ghost !p-2" title="Delete"
                      style="color:var(--sg-danger)">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-10" style="color:var(--sg-text-muted)">
                No sections have been created yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div data-empty-state="sections-table" class="hidden text-center py-12">
        <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
        <p class="text-sm" style="color:var(--sg-text-muted)">No sections match your search.</p>
      </div>
    </div>
  </div>
</div>
@endsection
