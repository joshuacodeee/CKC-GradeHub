@php
  $roleLabel = ['admin' => 'Administrator', 'teacher' => 'Teacher', 'student' => 'Student'][$role] ?? 'User';
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
      <button data-dropdown-toggle="notif-menu" class="sg-btn sg-btn-ghost !p-2 relative">
        <i data-lucide="bell" class="w-5 h-5"></i>
        <span class="absolute top-1 right-1 w-2 h-2 rounded-full" style="background:var(--sg-danger)"></span>
      </button>
      <div data-dropdown-menu="notif-menu" class="hidden absolute right-0 mt-2 w-80 sg-card p-2 z-40">
        <p class="px-3 py-2 text-xs font-semibold uppercase tracking-wider" style="color:var(--sg-text-muted)">
          Notifications</p>
        <a href="#" class="flex items-start gap-3 px-3 py-2.5 rounded-lg hover:bg-navy-50">
          <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
            style="background:var(--sg-info-bg)"><i data-lucide="file-check-2" class="w-4 h-4"
              style="color:var(--sg-navy-600)"></i></div>
          <div class="min-w-0">
            <p class="text-sm" style="color:var(--sg-text)">Grades submitted for BSIT 2-A</p>
            <p class="text-xs" style="color:var(--sg-text-muted)">5 hours ago</p>
          </div>
        </a>
        <a href="#" class="flex items-start gap-3 px-3 py-2.5 rounded-lg hover:bg-navy-50">
          <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
            style="background:var(--sg-success-bg)"><i data-lucide="user-plus" class="w-4 h-4"
              style="color:var(--sg-success)"></i></div>
          <div class="min-w-0">
            <p class="text-sm" style="color:var(--sg-text)">New student enrolled: Faith Navarro</p>
            <p class="text-xs" style="color:var(--sg-text-muted)">1 day ago</p>
          </div>
        </a>
      </div>
    </div>

    {{-- User menu --}}
    <div class="relative">
      <button data-dropdown-toggle="user-menu"
        class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-full hover:bg-navy-50 sg-transition">
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
          style="background:var(--sg-navy-700)" data-current-user-initials>
          {{ $role === 'admin' ? 'RV' : ($role === 'teacher' ? 'AR' : 'JD') }}
        </div>
        <i data-lucide="chevron-down" class="w-4 h-4 hidden sm:block" style="color:var(--sg-text-muted)"></i>
      </button>
      <div data-dropdown-menu="user-menu" class="hidden absolute right-0 mt-2 w-56 sg-card p-2 z-40">
        <div class="px-3 py-2 border-b mb-1" style="border-color:var(--sg-border)">
          <p class="text-sm font-semibold truncate" style="color:var(--sg-text)" data-current-user-name>
            {{ $role === 'admin' ? 'Dr. Ramon Villareal' : ($role === 'teacher' ? 'Prof. Andrea Reyes' : 'Juan Dela Cruz') }}
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