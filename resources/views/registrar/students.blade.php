@extends('layouts.app', ['role' => 'registrar', 'active' => 'students', 'title' => 'Student Records'])

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="font-display font-semibold text-xl" style="color:var(--sg-text)">Student Records</h1>
      <p class="text-sm" style="color:var(--sg-text-muted)">Search all student profiles, manage irregular status, and view complete academic records.</p>
    </div>

    <form method="GET" action="{{ route('registrar.students') }}" class="sg-card p-4 flex flex-wrap gap-3">
      <label class="sr-only" for="student-search">Search student records</label>
      <input id="student-search" type="search" name="search" value="{{ $search }}"
        placeholder="Search name, student ID, email, or course" class="sg-input flex-1 min-w-[240px]">
      <select name="standing" class="sg-select w-auto min-w-[170px]" aria-label="Filter by standing">
        <option value="">All students</option>
        <option value="irregular" @selected($standing === 'irregular')>Irregular students</option>
        <option value="regular" @selected($standing === 'regular')>Regular students</option>
      </select>
      <button class="sg-btn sg-btn-primary" type="submit"><i data-lucide="search" class="w-4 h-4"></i> Search</button>
      @if ($search !== '' || $standing !== '')
        <a href="{{ route('registrar.students') }}" class="sg-btn sg-btn-outline">Clear</a>
      @endif
    </form>

    <div class="sg-card p-5">
      <div class="overflow-x-auto">
        <table class="sg-table">
          <thead>
            <tr><th>Student ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year / Block</th><th>Standing</th><th></th></tr>
          </thead>
          <tbody>
            @forelse ($students as $student)
              <tr>
                <td>{{ $student->student_id ?? '—' }}</td>
                <td class="font-medium">{{ $student->last_name_first }}</td>
                <td>{{ $student->email }}</td>
                <td>{{ $student->course ?? '—' }}</td>
                <td>{{ $student->year_level ?? '—' }}{{ $student->block ? ' · ' . $student->block : '' }}</td>
                <td>
                  <span class="sg-badge {{ $student->is_irregular ? 'sg-badge-warning' : 'sg-badge-info' }}">
                    {{ $student->is_irregular ? 'Irregular' : 'Regular' }}
                  </span>
                </td>
                <td class="text-right">
                  <a href="{{ route('registrar.students.show', $student) }}" class="sg-btn sg-btn-outline !px-3 !py-1.5">
                    View records
                  </a>
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center py-10" style="color:var(--sg-text-muted)">No matching student records.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-4">{{ $students->links() }}</div>
    </div>
  </div>
@endsection
