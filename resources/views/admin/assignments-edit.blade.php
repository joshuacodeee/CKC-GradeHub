@extends('layouts.app', ['role' => 'admin', 'active' => 'assignments', 'title' => 'Edit Assignment'])

@section('content')
  <div class="space-y-5">
    <div>
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Edit Assignment</h2>
      <p class="text-sm" style="color:var(--sg-text-muted)">Update the teacher-subject-section assignment details.</p>
    </div>

    @if ($errors->any())
      <div class="sg-card p-4" style="background:var(--sg-danger-bg); border:1px solid var(--sg-danger)">
        <ul class="text-sm" style="color:var(--sg-danger)">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.assignments.update', $assignment) }}" class="sg-card p-6">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Teacher</label>
          <select name="teacher_id" class="sg-select" required>
            <option value="">— Select Teacher —</option>
            @foreach($teachers as $t)
              <option value="{{ $t->id }}" {{ old('teacher_id', $assignment->teacher_id) == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Subject</label>
          <select name="subject_id" class="sg-select" required>
            <option value="">— Select Subject —</option>
            @foreach($subjects as $s)
              <option value="{{ $s->id }}" {{ old('subject_id', $assignment->subject_id) == $s->id ? 'selected' : '' }}>{{ $s->code }} — {{ $s->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Section</label>
          <select name="section_id" class="sg-select" required>
            <option value="">— Select Section —</option>
            @foreach($sections as $sec)
              <option value="{{ $sec->id }}" {{ old('section_id', $assignment->section_id) == $sec->id ? 'selected' : '' }}>{{ $sec->name }}{{ $sec->block ? ' — Block ' . $sec->block : '' }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Year</label>
          <select name="school_year_id" class="sg-select" required>
            <option value="">— Select School Year —</option>
            @foreach($schoolYears as $sy)
              <option value="{{ $sy->id }}" {{ old('school_year_id', $assignment->school_year_id) == $sy->id ? 'selected' : '' }}>{{ $sy->name }} — {{ $sy->semester }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Semester</label>
          <select name="semester" class="sg-select" required>
            @foreach(['First Semester','Second Semester','Summer'] as $sem)
              <option value="{{ $sem }}" {{ old('semester', $assignment->semester) === $sem ? 'selected' : '' }}>{{ $sem }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Status</label>
          <select name="status" class="sg-select">
            <option value="active" {{ old('status', $assignment->status) === 'active' ? 'selected' : '' }}>Active</option>
            <option value="archived" {{ old('status', $assignment->status) === 'archived' ? 'selected' : '' }}>Archived</option>
          </select>
        </div>
      </div>

      <div class="flex justify-end gap-2 pt-4 mt-4 border-t" style="border-color:var(--sg-border)">
        <a href="{{ route('admin.assignments') }}" class="sg-btn sg-btn-outline">Cancel</a>
        <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save Changes</button>
      </div>
    </form>
  </div>
@endsection
