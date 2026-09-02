@extends('layouts.app', ['role' => 'admin', 'active' => 'curricula', 'title' => 'Edit Curriculum'])

@section('content')
@php
    $selectedIds = $curriculum->curriculumSubjects->pluck('subject_id')->all();
@endphp

<div class="space-y-5 max-w-4xl">
  <div class="flex items-center gap-3">
    <a href="{{ route('admin.curricula') }}" class="sg-btn sg-btn-ghost !p-2">
      <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Edit Curriculum</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">
        {{ $curriculum->course }} · {{ $curriculum->year_level }} · {{ $curriculum->semester }}
        @if($curriculum->schoolYear)· {{ $curriculum->schoolYear->name }}@endif
      </p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.curricula.update', $curriculum) }}" class="sg-card p-5 space-y-5">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Course</label>
        <select name="course" class="sg-select" required>
          @foreach ($courses as $course)
            <option value="{{ $course }}" @selected($course === $curriculum->course)>{{ $course }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Year Level</label>
        <select name="year_level" class="sg-select" required>
          @foreach ($yearLevels as $yl)
            <option value="{{ $yl }}" @selected($yl === $curriculum->year_level)>{{ $yl }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Semester</label>
        <select name="semester" class="sg-select" required>
          @foreach (['First Semester','Second Semester','Summer'] as $sem)
            <option value="{{ $sem }}" @selected($sem === $curriculum->semester)>{{ $sem }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Year <span style="color:var(--sg-text-muted)">(optional)</span></label>
        <select name="school_year_id" class="sg-select">
          <option value="">— All years —</option>
          @foreach ($schoolYears as $sy)
            <option value="{{ $sy->id }}" @selected($sy->id === $curriculum->school_year_id)>{{ $sy->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-2" style="color:var(--sg-text)">Subjects in this curriculum</label>
      <div class="space-y-2 max-h-96 overflow-y-auto sg-card p-3">
        @foreach ($subjects as $subject)
          @php $isSelected = in_array($subject->id, $selectedIds, true); @endphp
          <label class="flex items-center gap-3 px-2 py-1.5 rounded-md hover:bg-slate-50">
            <input type="checkbox" name="subjects[{{ $subject->id }}][selected]" value="1" class="subject-toggle" @checked($isSelected)>
            <span class="font-medium text-sm" style="color:var(--sg-text)">{{ $subject->code }}</span>
            <span class="text-sm" style="color:var(--sg-text-muted)">{{ $subject->name }}</span>
            <span class="ml-auto text-xs" style="color:var(--sg-text-muted)">{{ $subject->units }}u</span>
          </label>
        @endforeach
      </div>
    </div>

    <div>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked($curriculum->is_active) class="rounded">
        <span class="text-sm" style="color:var(--sg-text)">Active</span>
      </label>
    </div>

    <div class="flex justify-end gap-2 pt-4 border-t" style="border-color:var(--sg-border)">
      <a href="{{ route('admin.curricula') }}" class="sg-btn sg-btn-outline">Cancel</a>
      <button type="submit" class="sg-btn sg-btn-primary">
        <i data-lucide="check" class="w-4 h-4"></i> Save Changes
      </button>
    </div>
  </form>
</div>
@endsection
