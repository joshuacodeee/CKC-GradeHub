@php
  $roleLabel = ['admin' => 'Administrator', 'teacher' => 'Teacher', 'student' => 'Student'][$role] ?? 'User';
  $user = auth()->user();
  $userName = $user?->name ?? 'Account';
  $userInitials = collect(explode(' ', $userName))->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->join('');
  $navbarNotifications = $navbarNotifications ?? collect();
  $toneStyles = [
    'info'    => ['bg' => 'var(--sg-info-bg)',    'fg' => 'var(--sg-navy-600)'],
    'success' => ['bg' => 'var(--sg-success-bg)', 'fg' => 'var(--sg-success)'],
    'warning' => ['bg' => 'var(--sg-warning-bg)', 'fg' => 'var(--sg-warning)'],
    'danger'  => ['bg' => 'var(--sg-danger-bg)',  'fg' => 'var(--sg-danger)'],
  ];
@endphp
<header class="sticky top-0 z-30 h-16 flex items-center justify-between gap-3 px-4 sm:px-6 lg:px-8 border-b sg-surface"
  style="border-color:var(--sg-border)">
  <div class="flex items-center gap-3 min-w-0">
    <button data-sidebar-open class="lg:hidden sg-btn sg-btn-ghost !p-2">
      <i data-lucide="menu" class="w-5 h-5"></i>
    </button>
    <button data-sidebar-collapse class="hidden lg:inline-flex sg-btn sg-btn-ghost !p-2">
      <i data-lucide="panel-left" class="w-5 h-5"></i>
    </button>
    <div class="min-w-0">
      <h1 class="font-display font-bold text-lg sm:text-xl truncate" style="color:var(--sg-text)">{{ $title }}</h1>
      <p class="text-xs hidden sm:block" style="color:var(--sg-text-muted)">
        {{ $schoolInfoName ?? 'Christ the King College · Gingoog City' }} · S.Y. 2026–2027
      </p>
    </div>
  </div>

  <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
    {{-- Search --}}
    <div class="relative hidden md:block">
      <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2"
        style="color:var(--sg-text-muted)"></i>
      <input type="text" placeholder="Quick search…" class="sg-input !w-56 pl-9">
    </div>

    {{-- Theme toggle --}}
    <button data-theme-toggle class="sg-btn sg-btn-ghost !p-2" title="Toggle dark mode">
      <i data-lucide="sun-moon" class="w-5 h-5"></i>
    </button>

    {{-- Notifications --}}
    <div class="relative">
      <button data-dropdown-toggle="notif-menu" class="sg-btn sg-btn-ghost !p-2 relative"
        aria-label="Notifications">
        <i data-lucide="bell" class="w-5 h-5"></i>
        @if ($navbarNotifications->count() > 0)
          <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 rounded-full text-[10px] font-semibold text-white flex items-center justify-center"
            style="background:var(--sg-danger)">{{ $navbarNotifications->count() }}</span>
        @endif
      </button>
      <div data-dropdown-menu="notif-menu" class="hidden absolute right-0 mt-2 w-80 sg-card p-2 z-40">
        <div class="flex items-center justify-between px-3 py-2">
          <p class="text-xs font-semibold uppercase tracking-wider" style="color:var(--sg-text-muted)">
            Notifications
          </p>
          <span class="text-xs" style="color:var(--sg-text-muted)">
            {{ $navbarNotifications->count() }} {{ $navbarNotifications->count() === 1 ? 'new' : 'new' }}
          </span>
        </div>

        @forelse ($navbarNotifications as $notif)
          @php
            $tone = $toneStyles[$notif['tone'] ?? 'info'] ?? $toneStyles['info'];
            $icon = $notif['icon'] ?? 'info';
            $title = $notif['title'] ?? '';
            $time = $notif['time_human'] ?? '';
          @endphp
          <div class="flex items-start gap-3 px-3 py-2.5 rounded-lg hover:bg-navy-50">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
              style="background: {{ $tone['bg'] }}">
              <i data-lucide="{{ $icon }}" class="w-4 h-4" style="color: {{ $tone['fg'] }}"></i>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm" style="color:var(--sg-text)">{{ $title }}</p>
              <p class="text-xs" style="color:var(--sg-text-muted)">{{ $time }} ago</p>
            </div>
          </div>
        @empty
          <div class="px-3 py-8 text-center">
            <div class="w-10 h-10 rounded-full mx-auto mb-2 flex items-center justify-center"
              style="background:var(--sg-info-bg)">
              <i data-lucide="bell-off" class="w-5 h-5" style="color:var(--sg-navy-600)"></i>
            </div>
            <p class="text-sm font-medium" style="color:var(--sg-text)">You're all caught up</p>
            <p class="text-xs mt-1" style="color:var(--sg-text-muted)">No new notifications.</p>
          </div>
        @endforelse
      </div>
    </div>

    {{-- User menu --}}
    <div class="relative">
      <button data-dropdown-toggle="user-menu"
        class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-full hover:bg-navy-50 sg-transition">
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
          style="background:var(--sg-navy-700)" data-current-user-initials>
          {{ $userInitials }}
        </div>
        <i data-lucide="chevron-down" class="w-4 h-4 hidden sm:block" style="color:var(--sg-text-muted)"></i>
      </button>
      <div data-dropdown-menu="user-menu" class="hidden absolute right-0 mt-2 w-56 sg-card p-2 z-40">
        <div class="px-3 py-2 border-b mb-1" style="border-color:var(--sg-border)">
          <p class="text-sm font-semibold truncate" style="color:var(--sg-text)" data-current-user-name>
            {{ $userName }}
          </p>
          <p class="text-xs" style="color:var(--sg-text-muted)">{{ $roleLabel }}</p>
        </div>
        <a href="{{ route($role . '.profile') }}"
          class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg hover:bg-navy-50" style="color:var(--sg-text)">
          <i data-lucide="user" class="w-4 h-4"></i> My Profile
        </a>
        <button data-logout
          class="w-full flex items-center gap-2 px-3 py-2 text-sm rounded-lg hover:bg-navy-50 text-left"
          style="color:var(--sg-danger)">
          <i data-lucide="log-out" class="w-4 h-4"></i> Logout
        </button>
      </div>
    </div>
  </div>
</header>
