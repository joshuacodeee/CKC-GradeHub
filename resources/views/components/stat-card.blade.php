{{--
  Usage:
  @include('components.stat-card', [
    'label' => 'Total Students', 'value' => '1,248', 'icon' => 'users',
    'color' => 'navy', 'trend' => '+4.2%', 'trendUp' => true
  ])
--}}
@php
  $palette = [
    'navy'    => ['bg' => 'var(--sg-info-bg)',    'fg' => 'var(--sg-navy-600)'],
    'info'    => ['bg' => 'var(--sg-info-bg)',    'fg' => 'var(--sg-navy-600)'],
    'success' => ['bg' => 'var(--sg-success-bg)', 'fg' => 'var(--sg-success)'],
    'brass'   => ['bg' => 'var(--sg-brass-100)',  'fg' => 'var(--sg-brass-600)'],
    'warning' => ['bg' => 'var(--sg-warning-bg)', 'fg' => 'var(--sg-warning)'],
    'danger'  => ['bg' => 'var(--sg-danger-bg)',  'fg' => 'var(--sg-danger)'],
  ][$color ?? 'navy'];
@endphp
<div class="sg-card p-5 flex items-start justify-between">
  <div class="min-w-0">
    <p class="text-sm font-medium" style="color:var(--sg-text-muted)">{{ $label }}</p>
    <p class="font-display text-2xl sm:text-3xl font-bold mt-1" style="color:var(--sg-text)">{{ $value }}</p>
    @isset($trend)
      <p class="text-xs mt-2 inline-flex items-center gap-1" style="color: {{ $trendUp ? 'var(--sg-success)' : 'var(--sg-danger)' }}">
        <i data-lucide="{{ $trendUp ? 'trending-up' : 'trending-down' }}" class="w-3.5 h-3.5"></i> {{ $trend }}
        <span style="color:var(--sg-text-muted)">vs last term</span>
      </p>
    @endisset
  </div>
  <div class="sg-stat-icon" style="background: {{ $palette['bg'] }}">
    <i data-lucide="{{ $icon }}" class="w-5 h-5" style="color: {{ $palette['fg'] }}"></i>
  </div>
</div>
