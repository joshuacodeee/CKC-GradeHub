@extends('layouts.app', ['role' => 'admin', 'active' => 'assignments', 'title' => 'Assign Teacher']) 

@push('styles')
  {{-- Tom Select Styles --}}
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
  <style>
    /* Adjust Tom Select height and appearance to fit your UI theme */
    .ts-control {
      border-radius: 0.375rem;
      padding: 0.5rem 0.75rem;
      border-color: var(--sg-border, #d1d5db);
      background-color: transparent;
      color: var(--sg-text);
    }
    .ts-wrapper.single .ts-control {
      background-image: none;
    }
    .ts-dropdown {
      background-color: var(--sg-card-bg, #ffffff);
      color: var(--sg-text);
      border-color: var(--sg-border, #d1d5db);
      border-radius: 0.375rem;
      z-index: 50;
    }
    .ts-dropdown .active {
      background-color: var(--sg-primary-light, #f3f4f6);
      color: var(--sg-text);
    }
  </style>
@endpush

@section('content') 
  <div class="space-y-5"> 
    <div> 
      <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Assign Teacher to Subject</h2> 
      <p class="text-sm" style="color:var(--sg-text-muted)">Create a new teacher-subject-section assignment for the selected school year and semester.</p> 
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
 
    <form method="POST" action="{{ route('admin.assignments.store') }}" class="sg-card p-6"> 
      @csrf 
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4"> 
        <div> 
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Teacher</label> 
          <select id="teacher_id" name="teacher_id" class="sg-select" required> 
            <option value="">— Select Teacher —</option> 
            @foreach($teachers as $t) 
              <option value="{{ $t->id }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->email }})</option> 
            @endforeach 
          </select> 
        </div> 
        <div> 
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Subject</label> 
          <select id="subject_id" name="subject_id" class="sg-select" required> 
            <option value="">— Select Subject —</option> 
            @foreach($subjects as $s) 
              <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->code }} — {{ $s->name }}</option> 
            @endforeach 
          </select> 
        </div> 
        <div> 
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Section</label> 
          <select name="section_id" class="sg-select" required> 
            <option value="">— Select Section —</option> 
            @foreach($sections as $sec) 
              <option value="{{ $sec->id }}" {{ old('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name }}{{ $sec->block ? ' — Block ' . $sec->block : '' }} ({{ $sec->course }} · {{ $sec->year_level }})</option> 
            @endforeach 
          </select> 
        </div> 
        <div> 
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Year</label> 
          <select name="school_year_id" class="sg-select" required> 
            <option value="">— Select School Year —</option> 
            @foreach($schoolYears as $sy) 
              <option value="{{ $sy->id }}" {{ old('school_year_id') == $sy->id ? 'selected' : '' }}>{{ $sy->name }} — {{ $sy->semester }}</option> 
            @endforeach 
          </select> 
        </div> 
        <div> 
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Semester</label> 
          <select name="semester" class="sg-select" required> 
            @foreach(['First Semester','Second Semester','Summer'] as $sem) 
              <option value="{{ $sem }}" {{ old('semester', 'First Semester') === $sem ? 'selected' : '' }}>{{ $sem }}</option> 
            @endforeach 
          </select> 
        </div> 
        <div> 
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Status</label> 
          <select name="status" class="sg-select"> 
            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option> 
            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option> 
          </select> 
        </div> 
      </div> 
 
      <div class="flex justify-end gap-2 pt-4 mt-4 border-t" style="border-color:var(--sg-border)"> 
        <a href="{{ route('admin.assignments') }}" class="sg-btn sg-btn-outline">Cancel</a> 
        <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save Assignment</button> 
      </div> 
    </form> 
  </div> 
@endsection

@push('scripts')
  {{-- Tom Select JS --}}
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      new TomSelect('#teacher_id', {
        create: false,
        placeholder: 'Search teacher name or email...',
        sortField: { field: 'text', order: 'asc' }
      });

      new TomSelect('#subject_id', {
        create: false,
        placeholder: 'Search subject code or title...',
        sortField: { field: 'text', order: 'asc' }
      });
    });
  </script>
@endpush