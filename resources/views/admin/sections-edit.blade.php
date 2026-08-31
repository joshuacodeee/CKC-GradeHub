@extends('layouts.app', ['role' => 'admin', 'active' => 'sections', 'title' => 'Edit Section'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Edit Section: {{ $section->name }}</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">Modify section details</p>
  </div>

  <form method="POST" action="{{ route('admin.sections.update', $section) }}" class="sg-card p-6">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Section Name</label>
        <input type="text" name="name" required placeholder="e.g. BSIT 1-B" class="sg-input" value="{{ old('name', $section->name) }}">
        @error('name')
          <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
        @enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Course</label>
        <select name="course" class="sg-select" required>
          @foreach (['BS Elementary Education','BS Secondary Education','BS Business Administration','BS Accountancy','BS Information Technology','BS Social Work','BS Hospitality Management','BS Nursing','BS Criminology'] as $course)
            <option value="{{ $course }}" {{ old('course', $section->course) === $course ? 'selected' : '' }}>{{ $course }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Year Level</label>
        <select name="year_level" class="sg-select" required>
          <option value="1st Year" {{ old('year_level', $section->year_level) === '1st Year' ? 'selected' : '' }}>1st Year</option>
          <option value="2nd Year" {{ old('year_level', $section->year_level) === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
          <option value="3rd Year" {{ old('year_level', $section->year_level) === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
          <option value="4th Year" {{ old('year_level', $section->year_level) === '4th Year' ? 'selected' : '' }}>4th Year</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Block <span class="text-xs" style="color:var(--sg-text-muted)">(optional)</span></label>
        <input type="text" name="block" placeholder="e.g. A, B, C" value="{{ old('block', $section->block) }}" class="sg-input" maxlength="50">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Year</label>
        <select name="school_year_id" class="sg-select">
          <option value="">Select school year…</option>
          @foreach ($schoolYears as $sy)
            <option value="{{ $sy->id }}" {{ old('school_year_id', $section->school_year_id) == $sy->id ? 'selected' : '' }}>
              {{ $sy->name }} — {{ $sy->semester }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="sm:col-span-2">
        <label class="flex items-center gap-2 text-sm font-medium" style="color:var(--sg-text)">
          <input type="checkbox" name="is_active" value="1" {{ $section->is_active ? 'checked' : '' }} style="accent-color: var(--sg-navy-600)">
          Active section
        </label>
      </div>
    </div>

    <div class="flex justify-end gap-2 pt-4 mt-4 border-t" style="border-color:var(--sg-border)">
      <a href="{{ route('admin.sections') }}" class="sg-btn sg-btn-outline">Cancel</a>
      <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save Changes</button>
    </div>
  </form>
</div>
@endsection
