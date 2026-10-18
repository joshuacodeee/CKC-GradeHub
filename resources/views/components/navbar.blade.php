@php
  $roleKey = is_string($role) ? strtolower($role) : null;
  $roleLabel = ['admin' => 'Administrator', 'teacher' => 'Teacher', 'student' => 'Student'][$roleKey] ?? 'User';
  $user = auth()->user();
  $userName = $user?->name ?? 'Account';
  $userInitials = collect(explode(' ', $userName))->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->join('');
  $navbarNotifications = $navbarNotifications ?? collect();
  $navbarUnreadCount   = $navbarUnreadCount ?? 0;
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
    
    {{-- logo --}}
<!-- 
    <div class="w-10 h-10 rounded-full border overflow-hidden bg-white flex items-center justify-center flex-shrink-0"
      style="border-color:var(--sg-border)">
      @if (file_exists(public_path('logo/gradehub.jpg')))
        <img src="{{ asset('logo/gradehub.jpg') }}" alt="CKC GradeHub" class="w-full h-full object-contain">
      @else
        <span class="text-[10px] font-bold text-blue-900">CKC</span>
      @endif
    </div> -->

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
    <div class="relative" data-notif-root>
      <button data-dropdown-toggle="notif-menu" class="sg-btn sg-btn-ghost !p-2 relative"
        aria-label="Notifications" data-notif-bell>
        <i data-lucide="bell" class="w-5 h-5"></i>
        @if ($navbarUnreadCount > 0)
          <span data-notif-badge
            class="absolute top-1 right-1 min-w-[16px] h-4 px-1 rounded-full text-[10px] font-semibold text-white flex items-center justify-center"
            style="background:var(--sg-danger)">{{ $navbarUnreadCount }}</span>
        @endif
      </button>
      <div data-dropdown-menu="notif-menu" class="hidden absolute right-0 mt-2 w-80 sg-card p-2 z-40">
        <div class="flex items-center justify-between px-3 py-2">
          <p class="text-xs font-semibold uppercase tracking-wider" style="color:var(--sg-text-muted)">
            Notifications
          </p>
          <button type="button" data-notif-mark-all
            class="text-[11px] font-semibold uppercase tracking-wider hover:opacity-80 disabled:opacity-40 disabled:cursor-not-allowed"
            style="color:var(--sg-navy-600)"
            {{ $navbarUnreadCount === 0 ? 'disabled' : '' }}>
            Mark all read
          </button>
        </div>
        <p data-notif-status class="hidden px-3 pb-2 text-xs" role="status" aria-live="polite"></p>

        @forelse ($navbarNotifications as $notif)
          @php
            $tone = $toneStyles[$notif['tone'] ?? 'info'] ?? $toneStyles['info'];
            $icon = $notif['icon'] ?? 'info';
            $title = $notif['title'] ?? '';
            $time = $notif['time_human'] ?? '';
            $notifId = $notif['id'] ?? '';
            $isRead = $notif['read'] ?? false;
          @endphp
          <div class="notif-item group flex items-start gap-3 px-3 py-2.5 rounded-lg hover:bg-navy-50"
            data-notif-id="{{ $notifId }}"
            data-notif-read="{{ $isRead ? '1' : '0' }}">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
              style="background: {{ $tone['bg'] }}">
              <i data-lucide="{{ $icon }}" class="w-4 h-4" style="color: {{ $tone['fg'] }}"></i>
            </div>
            <div class="min-w-0 flex-1 {{ $isRead ? 'opacity-60' : '' }}">
              <p class="text-sm" style="color:var(--sg-text)">
                @unless ($isRead)
                  <span class="inline-block w-1.5 h-1.5 rounded-full mr-1.5 align-middle"
                    style="background:var(--sg-navy-600)"></span>
                @endunless
                {{ $title }}
              </p>
              <p class="text-xs" style="color:var(--sg-text-muted)">{{ $time }} ago</p>
            </div>
            @unless ($isRead)
              <button type="button" data-notif-mark
                data-notif-id="{{ $notifId }}"
                class="sg-btn sg-btn-ghost !p-1 opacity-0 group-hover:opacity-100 focus:opacity-100"
                title="Mark as read" aria-label="Mark as read">
                <i data-lucide="check" class="w-3.5 h-3.5"></i>
              </button>
            @endunless
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

@once
  @push('scripts')
    <script>
      (function () {
        const root = document.querySelector('[data-notif-root]');
        if (!root) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const badge = root.querySelector('[data-notif-badge]');
        const markAllBtn = root.querySelector('[data-notif-mark-all]');
        const status = root.querySelector('[data-notif-status]');

        const showStatus = (message, isError = false) => {
          if (!status) return;
          status.textContent = message;
          status.style.color = isError ? 'var(--sg-danger)' : 'var(--sg-success)';
          status.classList.toggle('hidden', !message);
        };

        const refreshBadge = () => {
          const unread = root.querySelectorAll('.notif-item[data-notif-read="0"]').length;
          if (unread === 0) {
            if (badge) badge.remove();
            if (markAllBtn) {
              markAllBtn.setAttribute('disabled', 'disabled');
            }
          } else {
            if (badge) {
              badge.textContent = unread;
            } else {
              const btn = root.querySelector('[data-notif-bell]');
              if (btn) {
                const span = document.createElement('span');
                span.setAttribute('data-notif-badge', '');
                span.className = 'absolute top-1 right-1 min-w-[16px] h-4 px-1 rounded-full text-[10px] font-semibold text-white flex items-center justify-center';
                span.style.background = 'var(--sg-danger)';
                span.textContent = unread;
                btn.appendChild(span);
              }
            }
            if (markAllBtn) markAllBtn.removeAttribute('disabled');
          }
        };

        const postJson = async (url, body) => {
          const response = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
            },
            body: JSON.stringify(body || {}),
            credentials: 'same-origin',
          });
          const data = await response.json();
          if (!response.ok || !data.ok) {
            throw new Error(data.message || 'Could not update notifications.');
          }
          return data;
        };

        const markOne = async (id) => {
          const safeId = typeof CSS !== 'undefined' && CSS.escape ? CSS.escape(id) : String(id).replace(/["\\]/g, '\\$&');
          const item = root.querySelector(`.notif-item[data-notif-id="${safeId}"]`);
          if (!item || item.dataset.notifRead === '1') return;
          const button = item.querySelector('[data-notif-mark]');
          if (button) button.disabled = true;
          try {
            await postJson('{{ route('notifications.read') }}', { id });
            item.dataset.notifRead = '1';
            const text = item.querySelector('.min-w-0');
            if (text) text.classList.add('opacity-60');
            const dot = text?.querySelector('span.inline-block');
            if (dot) dot.remove();
            const btn = item.querySelector('[data-notif-mark]');
            if (btn) btn.remove();
            refreshBadge();
            showStatus('Notification marked as read.');
          } catch (error) {
            console.error('Failed to mark notification as read.', error);
            showStatus('Could not mark the notification as read. Please try again.', true);
            if (button?.isConnected) button.disabled = false;
          }
        };

        root.addEventListener('click', (e) => {
          const oneBtn = e.target.closest('[data-notif-mark]');
          if (oneBtn) {
            e.preventDefault();
            e.stopPropagation();
            markOne(oneBtn.dataset.notifId);
            return;
          }
        });

        if (markAllBtn) {
          markAllBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            const items = root.querySelectorAll('.notif-item[data-notif-read="0"]');
            if (items.length === 0) return;
            const ids = Array.from(items).map((el) => el.dataset.notifId);
            markAllBtn.disabled = true;
            showStatus('');
            try {
              await postJson('{{ route('notifications.read-all') }}', { ids });
              items.forEach((el) => {
                el.dataset.notifRead = '1';
                const text = el.querySelector('.min-w-0');
                if (text) text.classList.add('opacity-60');
                const dot = text?.querySelector('span.inline-block');
                if (dot) dot.remove();
                const btn = el.querySelector('[data-notif-mark]');
                if (btn) btn.remove();
              });
              refreshBadge();
              showStatus('All notifications marked as read.');
            } catch (error) {
              console.error('Failed to mark all notifications as read.', error);
              showStatus('Could not mark notifications as read. Please try again.', true);
              markAllBtn.disabled = false;
            }
          });
        }
      })();
    </script>
  @endpush
@endonce
