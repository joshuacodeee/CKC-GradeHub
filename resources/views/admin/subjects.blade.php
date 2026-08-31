@extends('layouts.app', ['role' => 'admin', 'active' => 'subjects', 'title' => 'Subjects'])

@section('content')
  <div class="space-y-5">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Subject Catalog</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">{{ $subjects->count() }} subjects in the catalog</p>
    </div>

    <div class="sg-card p-5">
      @include('components.table', [
        'tableId' => 'subjects-table',
        'searchPlaceholder' => 'Search by subject code or name…',
        'filterColumn' => 'status',
        'filterOptions' => ['Active', 'Inactive'],
        'addLabel' => 'Add Subject',
        'addModalId' => 'add-subject-modal',
        'addIcon' => 'book-plus',
      ])

      <div class="overflow-x-auto -mx-5 px-5">
        <table class="sg-table" id="subjects-table">
          <thead>
            <tr>
              <th class="cursor-pointer" data-sort-column>Subject Code</th>
              <th class="cursor-pointer" data-sort-column>Subject Name</th>
              <th class="cursor-pointer" data-sort-column>Units</th>
              <th class="cursor-pointer" data-sort-column>Status</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($subjects as $subject)
              <tr>
                <td style="color:var(--sg-text)">{{ $subject->code }}</td>
                <td class="font-medium" style="color:var(--sg-text)">{{ $subject->name }}</td>
                <td style="color:var(--sg-text-muted)">{{ $subject->units }}</td>
                <td><span data-col="status"
                    class="sg-badge {{ $subject->is_active ? 'sg-badge-success' : 'sg-badge-danger' }}">{{ $subject->is_active ? 'Active' : 'Inactive' }}</span>
                </td>
                <td class="text-right">
                  <div class="flex justify-end gap-1">
                    <a href="{{ route('admin.subjects.show', $subject) }}" class="sg-btn sg-btn-ghost !p-2" title="View">
                      <i data-lucide="eye" class="w-4 h-4"></i>
                    </a>
                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="sg-btn sg-btn-ghost !p-2" title="Edit">
                      <i data-lucide="pencil" class="w-4 h-4"></i>
                    </a>
                    @if($subject->is_active)
                      <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
                        data-confirm="Deactivate subject {{ $subject->code }}? It will no longer appear in new assignments."
                        data-confirm-title="Deactivate Subject">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="sg-btn sg-btn-ghost !p-2" title="Deactivate"
                          style="color:var(--sg-danger)">
                          <i data-lucide="archive" class="w-4 h-4"></i>
                        </button>
                      </form>
                    @else
                      <form method="POST" action="{{ route('admin.subjects.activate', $subject) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="sg-btn sg-btn-ghost !p-2" title="Activate"
                          style="color:var(--sg-success)">
                          <i data-lucide="archive-restore" class="w-4 h-4"></i>
                        </button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-10" style="color:var(--sg-text-muted)">No subjects have been created yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
        <div data-empty-state="subjects-table" class="hidden text-center py-12">
          <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
          <p class="text-sm" style="color:var(--sg-text-muted)">No subjects match your search.</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Add Subject Modal --}}
  <div id="add-subject-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
    <div class="sg-modal sg-surface rounded-2xl w-full max-w-md">
      <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--sg-border)">
        <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Add New Subject</h3>
        <button data-modal-close="add-subject-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x"
            class="w-4 h-4"></i></button>
      </div>
      <form id="add-subject-form" method="POST" action="{{ route('admin.subjects.store') }}" class="p-6 space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Subject Code</label>
          <input type="text" name="code" required placeholder="e.g. IT401" class="sg-input">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Subject Name</label>
          <input type="text" name="name" required placeholder="e.g. Systems Integration" class="sg-input">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Description <span class="text-xs" style="color:var(--sg-text-muted)">(optional)</span></label>
          <textarea name="description" rows="2" placeholder="A short description of the course content." class="sg-input"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Units</label>
          <input type="number" name="units" min="1" max="6" required value="3" class="sg-input">
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t" style="border-color:var(--sg-border)">
          <button type="button" data-modal-close="add-subject-modal" class="sg-btn sg-btn-outline">Cancel</button>
          <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4 mr-2"></i>Save Subject</button>
        </div>
      </form>
    </div>
  </div>
@endsection
