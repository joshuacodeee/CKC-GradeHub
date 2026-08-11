{{--
  Static inline alert banner (distinct from the JS toast system).
  Usage:
  @include('components.notification', ['type' => 'info', 'title' => 'Heads up', 'message' => 'Grades are due Friday.'])
--}}
@php
  $type = $type ?? 'info';
  $styles = [
    'info'    => ['bg' => 'var(--sg-info-bg)',    'fg' => 'var(--sg-navy-600)', 'icon' => 'info'],
    'success' => ['bg' => 'var(--sg-success-bg)', 'fg' => 'var(--sg-success)',  'icon' => 'circle-check'],
    'warning' => ['bg' => 'var(--sg-warning-bg)', 'fg' => 'var(--sg-warning)',  'icon' => 'triangle-alert'],
    'danger'  => ['bg' => 'var(--sg-danger-bg)',  'fg' => 'var(--sg-danger)',   'icon' => 'circle-x'],
  ][$type];
@endphp
<div class="rounded-xl p-4 flex items-start gap-3 border" style="background: {{ $styles['bg'] }}; border-color: transparent;">
  <i data-lucide="{{ $styles['icon'] }}" class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: {{ $styles['fg'] }}"></i>
  <div class="min-w-0">
    @isset($title)<p class="text-sm font-semibold" style="color: {{ $styles['fg'] }}">{{ $title }}</p>@endisset
    <p class="text-sm mt-0.5" style="color:var(--sg-text)">{{ $message }}</p>
  </div>
</div>
