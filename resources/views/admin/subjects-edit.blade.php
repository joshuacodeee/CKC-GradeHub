@extends('layouts.app', ['role' => 'admin', 'active' => 'subjects', 'title' => 'Edit Subject'])

@section('content')
  <div class="space-y-5">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Edit Subject: {{ $subject->code }}</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">Update subject details.</p>
    </div>

    <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="sg-card p-6">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Subject Code</label>
          <input type="text" name="code" required value="{{ old('code', $subject->code) }}" class="sg-input">
          @error('code') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Subject Name</label>
          <input type="text" name="name" required value="{{ old('name', $subject->name) }}" class="sg-input">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Description <span class="text-xs" style="color:var(--sg-text-muted)">(optional)</span></label>
          <textarea name="description" rows="3" class="sg-input">{{ old('description', $subject->description) }}</textarea>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Units</label>
          <input type="number" name="units" min="1" max="6" required value="{{ old('units', $subject->units) }}" class="sg-input">
        </div>
        <div class="sm:col-span-2">
          <label class="flex items-center gap-2 text-sm font-medium" style="color:var(--sg-text)">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subject->is_active) ? 'checked' : '' }} style="accent-color: var(--sg-navy-600)">
            Active subject (uncheck to deactivate)
          </label>
        </div>
      </div>

      <div class="flex justify-end gap-2 pt-4 mt-4 border-t" style="border-color:var(--sg-border)">
        <a href="{{ route('admin.subjects') }}" class="sg-btn sg-btn-outline">Cancel</a>
        <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save Changes</button>
      </div>
    </form>
  </div>
@endsection
