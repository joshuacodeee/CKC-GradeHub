@extends('layouts.app', ['role' => 'admin', 'active' => 'dashboard', 'title' => 'Dashboard'])

@section('content')
  <div class="space-y-6" data-loading-wrap>

    {{-- Skeleton --}}
    <div data-skeleton class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      @for ($i = 0; $i < 4; $i++)
        <div class="sg-card p-5 h-[104px] sg-skeleton"></div>
      @endfor
    </div>

    <div data-content class="space-y-6">

      {{-- Current term banner --}}
      @include('components.notification', [
        'type' => 'info',
        'title' => 'Current Academic Term',
        'message' => 'School Year 2026–2027 · First Semester — Grade encoding deadline is Friday, August 14.',
      ])

      {{-- Stat cards --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @include('components.stat-card', ['label' => 'Total Students', 'value' => '1,248', 'icon' => 'users', 'color' => 'navy', 'trend' => '+4.2%', 'trendUp' => true])
        @include('components.stat-card', ['label' => 'Total Teachers', 'value' => '86', 'icon' => 'graduation-cap', 'color' => 'brass', 'trend' => '+1.1%', 'trendUp' => true])
        @include('components.stat-card', ['label' => 'Total Subjects', 'value' => '54', 'icon' => 'book-open', 'color' => 'success', 'trend' => '0.0%', 'trendUp' => true])
        @include('components.stat-card', ['label' => 'Total Sections', 'value' => '32', 'icon' => 'layers', 'color' => 'warning', 'trend' => '-2.4%', 'trendUp' => false])
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Grade distribution chart --}}
        <div class="sg-card p-5 xl:col-span-2">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="font-display font-semibold" style="color:var(--sg-text)">Grade Distribution</h3>
              <p class="text-xs" style="color:var(--sg-text-muted)">School-wide, current semester</p>
            </div>
            <span class="sg-badge sg-badge-info">All Sections</span>
          </div>
          <div id="grade-distribution-chart" class="space-y-3"></div>
        </div>

        {{-- Student performance overview --}}
        <div class="sg-card p-5">
          <h3 class="font-display font-semibold mb-1" style="color:var(--sg-text)">Performance Overview</h3>
          <p class="text-xs mb-4" style="color:var(--sg-text-muted)">Average GWA by year level</p>
          <div class="space-y-4">
            @foreach ([['1st Year', 86.4], ['2nd Year', 87.9], ['3rd Year', 89.1], ['4th Year', 90.6]] as [$yr, $avg])
              <div>
                <div class="flex justify-between text-sm mb-1">
                  <span style="color:var(--sg-text)">{{ $yr }}</span>
                  <span class="font-semibold" style="color:var(--sg-text)">{{ $avg }}</span>
                </div>
                <div class="h-2 rounded-full" style="background:var(--sg-navy-50)">
                  <div class="h-2 rounded-full" style="width: {{ $avg }}%; background: var(--sg-navy-600)"></div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Recent grade submissions --}}
        <div class="sg-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-semibold" style="color:var(--sg-text)">Recent Grade Submissions</h3>
            <a href="{{ route('admin.reports') }}" class="text-xs font-medium" style="color:var(--sg-navy-500)">View
              all</a>
          </div>
          <ul class="space-y-3" id="recent-submissions-list"></ul>
        </div>

        {{-- Recently added students --}}
        <div class="sg-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-semibold" style="color:var(--sg-text)">Recently Added Students</h3>
            <a href="{{ route('admin.students') }}" class="text-xs font-medium" style="color:var(--sg-navy-500)">View
              all</a>
          </div>
          <ul class="space-y-3" id="recent-students-list"></ul>
        </div>
      </div>

      {{-- Term summary --}}
      <div class="sg-card p-5 flex flex-wrap items-center gap-x-8 gap-y-3">
        <div>
          <p class="text-xs" style="color:var(--sg-text-muted)">Current School Year</p>
          <p class="font-display font-semibold" style="color:var(--sg-text)">2026–2027</p>
        </div>
        <div class="h-8 w-px hidden sm:block" style="background:var(--sg-border)"></div>
        <div>
          <p class="text-xs" style="color:var(--sg-text-muted)">Current Semester</p>
          <p class="font-display font-semibold" style="color:var(--sg-text)">First Semester</p>
        </div>
        <div class="h-8 w-px hidden sm:block" style="background:var(--sg-border)"></div>
        <div>
          <p class="text-xs" style="color:var(--sg-text-muted)">Enrollment Status</p><span
            class="sg-badge sg-badge-success">Open</span>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    // Mock data placeholder
  </script>
@endpush