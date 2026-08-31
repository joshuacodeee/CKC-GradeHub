@extends('layouts.app', ['role' => 'admin', 'active' => 'dashboard', 'title' => 'Dashboard'])

@section('content')
  <div class="space-y-6">

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
      @include('components.stat-card', ['label' => 'Students', 'value' => $studentCount, 'icon' => 'users', 'color' => 'navy'])
      @include('components.stat-card', ['label' => 'Teachers', 'value' => $teacherCount, 'icon' => 'graduation-cap', 'color' => 'brass'])
      @include('components.stat-card', ['label' => 'Subjects', 'value' => $subjectCount, 'icon' => 'book-open', 'color' => 'navy'])
      @include('components.stat-card', ['label' => 'Sections', 'value' => $sectionCount, 'icon' => 'layers', 'color' => 'brass'])
      @include('components.stat-card', ['label' => 'Active Assignments', 'value' => $assignmentCount, 'icon' => 'user-check', 'color' => 'success'])
      @include('components.stat-card', ['label' => 'Grade Records', 'value' => $gradeRecordCount, 'icon' => 'file-check-2', 'color' => 'navy'])
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

      {{-- Grade distribution chart --}}
      <div class="sg-card p-5 xl:col-span-2">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="font-display font-semibold" style="color:var(--sg-text)">Grade Distribution</h3>
            <p class="text-xs" style="color:var(--sg-text-muted)">
              {{ $schoolYear?->name ?? 'All school years' }} ·
              {{ $totalGraded }} {{ \Illuminate\Support\Str::plural('grade', $totalGraded) }} recorded
            </p>
          </div>
          <span class="sg-badge sg-badge-info">All Sections</span>
        </div>

        @if ($totalGraded > 0)
          <div class="space-y-3" data-grade-distribution>
            @php
              $bandStyles = [
                '90-100'   => ['bar' => 'var(--sg-success)', 'chip' => 'var(--sg-success-bg)', 'fg' => 'var(--sg-success)'],
                '85-89'    => ['bar' => 'var(--sg-navy-500)', 'chip' => 'var(--sg-info-bg)',    'fg' => 'var(--sg-navy-600)'],
                '80-84'    => ['bar' => 'var(--sg-brass-500)','chip' => 'var(--sg-warning-bg)', 'fg' => 'var(--sg-warning)'],
                '75-79'    => ['bar' => '#a9761f',           'chip' => 'var(--sg-warning-bg)', 'fg' => 'var(--sg-warning)'],
                'Below 75' => ['bar' => 'var(--sg-danger)',   'chip' => 'var(--sg-danger-bg)',  'fg' => 'var(--sg-danger)'],
              ];
            @endphp
            @foreach ($gradeDistribution as $band => $count)
              @php
                $pct = $gradeDistributionPct[$band] ?? 0;
                $style = $bandStyles[$band] ?? $bandStyles['Below 75'];
              @endphp
              <div>
                <div class="flex items-center justify-between text-xs mb-1">
                  <span class="font-medium" style="color:var(--sg-text)">{{ $band }}</span>
                  <span style="color:var(--sg-text-muted)">
                    {{ $count }} {{ \Illuminate\Support\Str::plural('student', $count) }} · {{ $pct }}%
                  </span>
                </div>
                <div class="h-2.5 rounded-full overflow-hidden" style="background:var(--sg-border)">
                  <div class="h-full rounded-full sg-transition"
                    style="width: {{ max($pct, 2) }}%; background: {{ $style['bar'] }}"></div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="py-10 text-center text-sm" style="color:var(--sg-text-muted)">
            Grade distribution chart will be displayed once grades are recorded.
          </div>
        @endif
      </div>

      {{-- Quick actions --}}
      <div class="sg-card p-5">
        <h3 class="font-display font-semibold mb-3" style="color:var(--sg-text)">Quick Actions</h3>
        <div class="space-y-2">
          <a href="{{ route('admin.students') }}" class="sg-btn sg-btn-outline w-full !justify-start"><i data-lucide="user-plus" class="w-4 h-4"></i> Add Student</a>
          <a href="{{ route('admin.teachers') }}" class="sg-btn sg-btn-outline w-full !justify-start"><i data-lucide="graduation-cap" class="w-4 h-4"></i> Add Teacher</a>
          <a href="{{ route('admin.subjects') }}" class="sg-btn sg-btn-outline w-full !justify-start"><i data-lucide="book-plus" class="w-4 h-4"></i> Add Subject</a>
          <a href="{{ route('admin.sections.create') }}" class="sg-btn sg-btn-outline w-full !justify-start"><i data-lucide="layers" class="w-4 h-4"></i> Add Section</a>
          <a href="{{ route('admin.assignments.create') }}" class="sg-btn sg-btn-outline w-full !justify-start"><i data-lucide="user-check" class="w-4 h-4"></i> Assign Teacher</a>
          <a href="{{ route('admin.reports') }}" class="sg-btn sg-btn-outline w-full !justify-start"><i data-lucide="file-bar-chart-2" class="w-4 h-4"></i> View Grade Reports</a>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

      {{-- Recent grade submissions --}}
      <div class="sg-card p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-display font-semibold" style="color:var(--sg-text)">Recent Grade Submissions</h3>
          <a href="{{ route('admin.reports') }}" class="text-xs font-medium" style="color:var(--sg-navy-500)">View all</a>
        </div>

        @forelse ($recentGradeSubmissions as $grade)
          @php
            $studentName = $grade->studentUser?->name ?? '—';
            $subjectCode = $grade->subject?->code ?? '—';
            $teacherName = $grade->teacher?->name ?? '—';
            $finalGrade  = $grade->final_grade !== null ? number_format((float) $grade->final_grade, 2) : '—';
            $submittedAt = $grade->submitted_at ?? $grade->updated_at;
            $remarks = $grade->remarks;
          @endphp
          <div class="flex items-center justify-between gap-3 py-2.5 border-b last:border-0"
            style="border-color:var(--sg-border)">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
                style="background:var(--sg-info-bg)">
                <i data-lucide="file-check-2" class="w-4 h-4" style="color:var(--sg-navy-600)"></i>
              </div>
              <div class="min-w-0">
                <p class="text-sm font-medium truncate" style="color:var(--sg-text)">
                  {{ $studentName }}
                  <span style="color:var(--sg-text-muted)">·</span>
                  <span class="font-normal" style="color:var(--sg-text-muted)">{{ $subjectCode }}</span>
                </p>
                <p class="text-xs truncate" style="color:var(--sg-text-muted)">
                  Submitted by {{ $teacherName }}
                  @if($remarks)
                    · <span class="italic">{{ ucfirst($remarks) }}</span>
                  @endif
                </p>
              </div>
            </div>
            <div class="text-right flex-shrink-0">
              <p class="text-sm font-semibold" style="color:var(--sg-navy-700)">{{ $finalGrade }}</p>
              <p class="text-xs" style="color:var(--sg-text-muted)">
                {{ $submittedAt ? $submittedAt->diffForHumans(null, true, true) : '—' }}
              </p>
            </div>
          </div>
        @empty
          <p class="py-10 text-center text-sm" style="color:var(--sg-text-muted)">No grade submissions yet.</p>
        @endforelse
      </div>

      {{-- Recently added students --}}
      <div class="sg-card p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-display font-semibold" style="color:var(--sg-text)">Recently Added Students</h3>
          <a href="{{ route('admin.students') }}" class="text-xs font-medium" style="color:var(--sg-navy-500)">View all</a>
        </div>
        @forelse ($recentStudents as $student)
          <div class="flex items-center justify-between gap-3 py-2 border-b last:border-0"
            style="border-color:var(--sg-border)">
            <div class="min-w-0">
              <p class="text-sm font-medium truncate" style="color:var(--sg-text)">{{ $student->name }}</p>
              <p class="text-xs truncate" style="color:var(--sg-text-muted)">{{ $student->student_id }}</p>
            </div>
            <span class="text-xs" style="color:var(--sg-text-muted)">{{ $student->created_at->format('M d, Y') }}</span>
          </div>
        @empty
          <p class="py-10 text-center text-sm" style="color:var(--sg-text-muted)">No student accounts yet.</p>
        @endforelse
      </div>
    </div>

  </div>
@endsection
