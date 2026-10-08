@extends('layouts.app', ['role' => 'admin', 'active' => 'curricula', 'title' => 'New Curriculum'])

@section('content')
<div class="space-y-5 max-w-4xl">
  <div class="flex items-center gap-3">
    <a href="{{ route('admin.curricula') }}" class="sg-btn sg-btn-ghost !p-2">
      <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">New Curriculum</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">Define the subjects for one (course, year, semester) combination.</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.curricula.store') }}" class="sg-card p-5 space-y-5">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Course</label>
        <select name="course" class="sg-select" required>
          @foreach ($courses as $course)
            <option value="{{ $course }}">{{ $course }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Year Level</label>
        <select name="year_level" class="sg-select" required>
          @foreach ($yearLevels as $yl)
            <option value="{{ $yl }}">{{ $yl }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Semester</label>
        <select name="semester" class="sg-select" required>
          <option>First Semester</option>
          <option>Second Semester</option>
          <option>Summer</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Year <span style="color:var(--sg-text-muted)">(optional)</span></label>
        <select name="school_year_id" class="sg-select">
          <option value="">— All years —</option>
          @foreach ($schoolYears as $sy)
            <option value="{{ $sy->id }}">{{ $sy->name }}</option>
          @endforeach
        </select>
        <p class="text-xs mt-1" style="color:var(--sg-text-muted)">Leave blank for a year-agnostic default.</p>
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between gap-3 mb-2">
        <label class="block text-sm font-medium" style="color:var(--sg-text)">Subjects in this curriculum</label>
        <button type="button" id="toggle-all-btn" class="text-xs font-medium underline" style="color:var(--sg-text-muted)">
          Select All
        </button>
      </div>

      {{-- Search Bar Input --}}
      <div class="relative mb-2">
        <input 
          type="text" 
          id="subject-search-input" 
          placeholder="Search subject code or name..." 
          class="sg-select text-sm pl-9"
        >
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" style="color:var(--sg-text-muted)"></i>
      </div>

      {{-- Checkbox List Container --}}
      <div id="subject-list" class="space-y-2 max-h-96 overflow-y-auto sg-card p-3">
        @foreach ($subjects as $subject)
          <label class="subject-item flex items-center gap-3 px-2 py-1.5 rounded-md hover:bg-slate-50 cursor-pointer" data-search="{{ strtolower($subject->code . ' ' . $subject->name) }}">
            <input type="checkbox" name="subjects[{{ $subject->id }}][selected]" value="1" class="subject-toggle">
            <span class="font-medium text-sm" style="color:var(--sg-text)">{{ $subject->code }}</span>
            <span class="text-sm" style="color:var(--sg-text-muted)">{{ $subject->name }}</span>
            <span class="ml-auto text-xs" style="color:var(--sg-text-muted)">{{ $subject->units }}u</span>
          </label>
        @endforeach

        <div id="no-subjects-found" class="hidden text-center py-4 text-sm" style="color:var(--sg-text-muted)">
          No subjects found matching your search.
        </div>
      </div>
      <p class="text-xs mt-2" style="color:var(--sg-text-muted)">Tick each subject that a regular student in this curriculum should take.</p>
    </div>

    <div class="flex justify-end gap-2 pt-4 border-t" style="border-color:var(--sg-border)">
      <a href="{{ route('admin.curricula') }}" class="sg-btn sg-btn-outline">Cancel</a>
      <button type="submit" class="sg-btn sg-btn-primary">
        <i data-lucide="check" class="w-4 h-4"></i> Save Curriculum
      </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('subject-search-input');
    const subjectItems = document.querySelectorAll('.subject-item');
    const noResults = document.getElementById('no-subjects-found');
    const toggleAllBtn = document.getElementById('toggle-all-btn');

    // Real-time filtering function
    searchInput.addEventListener('input', function () {
      const query = this.value.toLowerCase().trim();
      let visibleCount = 0;

      subjectItems.forEach(item => {
        const searchText = item.getAttribute('data-search');
        if (searchText.includes(query)) {
          item.classList.remove('hidden');
          item.classList.add('flex');
          visibleCount++;
        } else {
          item.classList.add('hidden');
          item.classList.remove('flex');
        }
      });

      if (visibleCount === 0) {
        noResults.classList.remove('hidden');
      } else {
        noResults.classList.add('hidden');
      }
    });

    // Toggle All / Deselect All functionality for visible subjects
    toggleAllBtn.addEventListener('click', function () {
      const visibleCheckboxes = Array.from(subjectItems)
        .filter(item => !item.classList.contains('hidden'))
        .map(item => item.querySelector('.subject-toggle'));

      const allChecked = visibleCheckboxes.every(cb => cb.checked);

      visibleCheckboxes.forEach(cb => {
        cb.checked = !allChecked;
      });

      this.textContent = allChecked ? 'Select All' : 'Deselect All';
    });
  });
</script>
@endpush