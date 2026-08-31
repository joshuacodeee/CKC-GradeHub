@extends('layouts.app', ['role' => 'admin', 'active' => 'sections', 'title' => 'Add Section'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Add New Section</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">Create a new section for student enrollment</p>
  </div>

  <form method="POST" action="{{ route('admin.sections.store') }}" class="sg-card p-6">
    @csrf
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Section Name</label>
        <input type="text" name="name" required placeholder="e.g. BSIT 1-B" class="sg-input" value="{{ old('name') }}">
        @error('name')
          <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
        @enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Course</label>
        <select name="course" class="sg-select" required>
          @foreach (['BS Elementary Education','BS Secondary Education','BS Business Administration','BS Accountancy','BS Information Technology','BS Social Work','BS Hospitality Management','BS Nursing','BS Criminology'] as $course)
            <option value="{{ $course }}" {{ old('course') === $course ? 'selected' : '' }}>{{ $course }}</option>
          @endforeach
        </select>
        @error('course')
          <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
        @enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Year Level</label>
        <select name="year_level" class="sg-select" required>
          <option value="1st Year" {{ old('year_level') === '1st Year' ? 'selected' : '' }}>1st Year</option>
          <option value="2nd Year" {{ old('year_level') === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
          <option value="3rd Year" {{ old('year_level') === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
          <option value="4th Year" {{ old('year_level') === '4th Year' ? 'selected' : '' }}>4th Year</option>
        </select>
        @error('year_level')
          <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
        @enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Block <span class="text-xs" style="color:var(--sg-text-muted)">(optional)</span></label>
        <input type="text" name="block" placeholder="e.g. A, B, C" value="{{ old('block') }}" class="sg-input" maxlength="50">
        @error('block')
          <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
        @enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Year</label>
        <select name="school_year_id" class="sg-select">
          <option value="">Select school year…</option>
          @foreach ($schoolYears as $sy)
            <option value="{{ $sy->id }}" {{ old('school_year_id') === (string)$sy->id ? 'selected' : '' }}>
              {{ $sy->name }} — {{ $sy->semester }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="flex justify-end gap-2 pt-4 mt-4 border-t" style="border-color:var(--sg-border)">
      <a href="{{ route('admin.sections') }}" class="sg-btn sg-btn-outline">Cancel</a>
      <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save Section</button>
    </div>
  </form>
</div>
@endsection
