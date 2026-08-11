{{--
  Generic reusable modal shell.
  Usage:
  @include('components.modal', [
    'id' => 'add-student-modal', 'title' => 'Add Student', 'icon' => 'user-plus', 'maxWidth' => 'max-w-lg'
  ])
  @slot ... not used in plain Blade includes; instead wrap the include call site's
  body using @section-style slots is unnecessary here — pages compose the inner
  form markup directly around this shell where noted.
--}}
<div id="{{ $id }}" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
  <div class="sg-modal sg-surface rounded-2xl w-full {{ $maxWidth ?? 'max-w-lg' }} max-h-[90vh] flex flex-col">
    <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0" style="border-color:var(--sg-border)">
      <div class="flex items-center gap-2.5">
        @isset($icon)
        <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:var(--sg-info-bg)">
          <i data-lucide="{{ $icon }}" class="w-4.5 h-4.5" style="color:var(--sg-navy-600)"></i>
        </div>
        @endisset
        <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">{{ $title }}</h3>
      </div>
      <button data-modal-close="{{ $id }}" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    <div class="p-6 overflow-y-auto">
      {{ $slot ?? '' }}
    </div>
  </div>
</div>
