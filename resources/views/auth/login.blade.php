<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sign In · CKC GradeHub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
    rel="stylesheet">
  <script src="{{ asset('css/tailwind.css') }}"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="/css/custom.css">
  <style>
    .auth-panel-bg {
      background-image:
        radial-gradient(ellipse 60% 50% at 20% 15%, rgba(200, 149, 47, 0.18), transparent 60%),
        radial-gradient(ellipse 70% 60% at 95% 90%, rgba(37, 99, 163, 0.25), transparent 60%),
        linear-gradient(160deg, var(--sg-navy-900), var(--sg-navy-700));
    }

    .auth-rule {
      width: 48px;
      height: 2px;
      background: var(--sg-brass-500);
      display: inline-block;
    }

    .auth-display {
      font-family: 'Sora', 'Inter', ui-sans-serif, system-ui, sans-serif;
      letter-spacing: -0.02em;
      line-height: 1.05;
    }

    .auth-mock-card {
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.10);
      backdrop-filter: blur(8px);
    }

    .auth-mock-row {
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .auth-grid {
      background-image:
        linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
      background-size: 32px 32px;
    }

    @keyframes auth-float-a {

      0%,
      100% {
        transform: translateY(0px);
      }

      50% {
        transform: translateY(-6px);
      }
    }

    @keyframes auth-float-b {

      0%,
      100% {
        transform: translateY(0px);
      }

      50% {
        transform: translateY(8px);
      }
    }

    .auth-float-a {
      animation: auth-float-a 6s ease-in-out infinite;
    }

    .auth-float-b {
      animation: auth-float-b 7s ease-in-out infinite;
    }

    input::-ms-reveal,
    input::-ms-clear {
      display: none;
    }
  </style>
</head>

<body class="antialiased" style="background:var(--sg-bg)">

  @php
    // Real, live data shared by AppServiceProvider for the auth views.
    $schoolYearName = $authSchoolYear?->name ?? 'S.Y. ' . date('Y') . '–' . (date('Y') + 1);
    $weightedAverage = $authWeightedAverage !== null ? round((float) $authWeightedAverage, 1) : null;
    $hasData = $authGradeCount > 0;
    $topStudent = $authTopStudent;
    $topInitials = $topStudent
      ? collect(explode(' ', $topStudent['name']))->filter()->take(2)
        ->map(fn($p) => strtoupper(substr($p, 0, 1)))->join('')
      : null;
  @endphp

  <div class="min-h-screen grid lg:grid-cols-2">

    {{-- Left: brand / illustration panel --}}
    <aside
      class="hidden lg:flex flex-col justify-between p-12 xl:p-16 relative overflow-hidden auth-panel-bg text-white">

      <div class="absolute inset-0 auth-grid opacity-40 pointer-events-none"></div>
      <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full opacity-20 pointer-events-none"
        style="background: var(--sg-brass-500); filter: blur(40px);"></div>
      <div class="absolute -left-32 -bottom-32 w-[28rem] h-[28rem] rounded-full opacity-20 pointer-events-none"
        style="background: var(--sg-navy-500); filter: blur(40px);"></div>

      <a href="{{ route('landing') }}" class="relative flex items-center gap-3 group">
        <img src="{{ asset('logo/gradehub.jpg') }}" alt="CKC GradeHub" class="w-14 h-14 object-contain rounded-full border border-white/10">
        <span class="leading-tight">
          <strong class="font-display block text-base">GradeHub</strong>
          <small class="text-xs text-white/60">Christ the King College</small>
        </span>
      </a>

      <div class="relative max-w-lg">
        <div class="flex items-center gap-3 mb-6">
          <span class="auth-rule"></span>
          <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color:var(--sg-brass-500)">
            Welcome back
          </p>
        </div>
        <h1 class="auth-display text-4xl xl:text-5xl font-bold mb-4">
          One workspace for the<br>
          <span style="color:var(--sg-brass-500)">whole grading term.</span>
        </h1>
        <p class="text-white/65 text-base leading-relaxed max-w-md">
          Sign in to your CKC GradeHub account to record grades, post subjects, and follow academic progress
          across the school year.
        </p>

        {{-- Live term overview card --}}
        <div class="relative mt-12">
          <div class="auth-mock-card rounded-2xl p-5 shadow-2xl auth-float-a">
            <div class="flex items-center justify-between mb-4">
              <p class="text-xs uppercase tracking-wider text-white/50">Term overview</p>
              <span class="text-[10px] font-semibold uppercase tracking-wider"
                style="color:var(--sg-brass-500)">{{ $schoolYearName }}</span>
            </div>
            <div class="space-y-2.5">
              <div class="auth-mock-row rounded-lg p-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-md flex items-center justify-center"
                    style="background:rgba(200,149,47,0.15)">
                    <i data-lucide="file-check-2" class="w-4 h-4" style="color:var(--sg-brass-500)"></i>
                  </div>
                  <div>
                    <p class="text-sm font-semibold">Final grades posted</p>
                    <p class="text-[11px] text-white/50">{{ $authGradesThisWeek }} this week</p>
                  </div>
                </div>
                <p class="font-display font-bold text-lg">{{ number_format($authGradeCount) }}</p>
              </div>
              <div class="auth-mock-row rounded-lg p-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-md flex items-center justify-center"
                    style="background:rgba(37,99,163,0.30)">
                    <i data-lucide="users" class="w-4 h-4" style="color:#9ec0e8"></i>
                  </div>
                  <div>
                    <p class="text-sm font-semibold">Active students</p>
                    <p class="text-[11px] text-white/50">Across all programs</p>
                  </div>
                </div>
                <p class="font-display font-bold text-lg">{{ number_format($authStudentCount) }}</p>
              </div>
              <div class="auth-mock-row rounded-lg p-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-md flex items-center justify-center"
                    style="background:rgba(26,138,83,0.20)">
                    <i data-lucide="layout-dashboard" class="w-4 h-4" style="color:#5fc593"></i>
                  </div>
                  <div>
                    <p class="text-sm font-semibold">Active sections</p>
                    <p class="text-[11px] text-white/50">Currently in session</p>
                  </div>
                </div>
                <p class="font-display font-bold text-lg">{{ number_format($authSectionCount) }}</p>
              </div>
            </div>
          </div>

          {{-- Floating chip: top student (or general weighted average when no top student) --}}
          @if ($topStudent)
            <div
              class="auth-mock-card absolute -right-6 -top-6 rounded-xl px-4 py-3 shadow-xl auth-float-b max-w-[14rem]">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-display font-bold text-[11px]"
                  style="background:linear-gradient(140deg,var(--sg-brass-500),var(--sg-brass-600)); color:var(--sg-navy-900)">
                  {{ $topInitials }}
                </div>
                <div class="leading-tight min-w-0">
                  <p class="text-xs font-semibold truncate">Top: {{ $topStudent['name'] }}</p>
                  <p class="text-[10px] text-white/50">
                    GWA {{ number_format($topStudent['average'], 1) }} ·
                    {{ $topStudent['grade_count'] }}
                    {{ \Illuminate\Support\Str::plural('grade', $topStudent['grade_count']) }}
                  </p>
                </div>
              </div>
            </div>
          @elseif ($weightedAverage !== null)
            <div class="auth-mock-card absolute -right-6 -top-6 rounded-xl px-4 py-3 shadow-xl auth-float-b">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                  style="background:rgba(26,138,83,0.20)">
                  <i data-lucide="circle-check" class="w-4 h-4" style="color:#5fc593"></i>
                </div>
                <div class="leading-tight">
                  <p class="text-xs font-semibold">Term GWA</p>
                  <p class="text-[10px] text-white/50">{{ number_format($weightedAverage, 1) }} weighted</p>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>

      <div class="relative flex items-center gap-6 text-white/40 text-xs">
        <span>© {{ date('Y') }} Christ the King College</span>
        <span class="w-px h-3" style="background:rgba(255,255,255,0.15)"></span>
        <span>Gingoog City · {{ $schoolYearName }}</span>
      </div>
    </aside>

    {{-- Right: login form --}}
    <main class="flex items-center justify-center p-6 sm:p-10 relative">
      <div class="w-full max-w-md">

        {{-- Top nav row (mobile brand + back link) --}}
        <div class="flex items-center justify-between mb-10">
          {{-- Mobile brand --}}
          <div class="lg:hidden flex items-center gap-3">
            <img src="{{ asset('logo/gradehub.jpg') }}" alt="CKC GradeHub" class="w-14 h-14 object-contain rounded-full border border-white/10">
            <div class="leading-tight">
              <p class="font-display font-bold text-base" style="color:var(--sg-text)">GradeHub</p>
              <p class="text-xs" style="color:var(--sg-text-muted)">Christ the King College</p>
            </div>
          </div>
          {{-- Back to home --}}
          <a href="{{ route('landing') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium hover:opacity-80 ml-auto"
            style="color:var(--sg-text-muted)">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to home
          </a>
        </div>

        {{-- Heading --}}
        <div class="mb-8">
          <p class="text-xs font-semibold uppercase tracking-[0.22em] mb-2" style="color:var(--sg-brass-600)">Sign in
          </p>
          <h2 class="auth-display text-3xl sm:text-4xl font-bold" style="color:var(--sg-text)">
            Welcome back.
          </h2>
          <p class="text-sm mt-2" style="color:var(--sg-text-muted)">
            Use your email or student ID to access your dashboard.
          </p>
        </div>

        {{-- Session errors --}}
        @if ($errors->any())
          <div class="mb-5 rounded-xl p-3.5 flex items-start gap-3 border"
            style="background:var(--sg-danger-bg); border-color:var(--sg-danger) !important;">
            <i data-lucide="circle-x" class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:var(--sg-danger)"></i>
            <p class="text-sm" style="color:var(--sg-text)">
              {{ $errors->first() ?: 'Please check your credentials and try again.' }}
            </p>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5" novalidate>
          @csrf

          <div>
            <label for="login_id" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">
              Email or Student ID
            </label>
            <div class="relative">
              <i data-lucide="mail" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                style="color:var(--sg-text-muted)"></i>
              <input type="text" id="login_id" name="login_id" required autocomplete="username"
                value="{{ old('login_id') }}" placeholder="you@ckcgingoog.edu.ph" class="sg-input !pl-10 !py-3">
            </div>
            @error('login_id')
              <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
              </p>
            @enderror
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="password" class="block text-sm font-medium" style="color:var(--sg-text)">
                Password
              </label>
              <a href="{{ route('password.forgot') }}" class="text-xs font-medium"
                style="color:var(--sg-navy-600)">Forgot?</a>
            </div>
            <div class="relative">
              <i data-lucide="lock" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                style="color:var(--sg-text-muted)"></i>
              <input type="password" id="password" name="password" required autocomplete="current-password"
                placeholder="••••••••" class="sg-input !pl-10 !pr-11 !py-3">
              <button type="button" data-password-toggle="password"
                class="absolute right-3 top-1/2 -translate-y-1/2 inline-flex items-center justify-center w-7 h-7 rounded-md hover:bg-navy-50"
                style="color:var(--sg-text-muted)" aria-label="Toggle password visibility">
                <i data-lucide="eye" class="w-4 h-4" data-eye-open></i>
                <i data-lucide="eye-off" class="w-4 h-4 hidden" data-eye-closed></i>
              </button>
            </div>
            @error('password')
              <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
              </p>
            @enderror
          </div>

          <label class="flex items-center gap-2.5 text-sm select-none cursor-pointer pt-1" style="color:var(--sg-text)">
            <input type="checkbox" name="remember"
              class="w-4 h-4 rounded border-slate-300 focus:ring-2 focus:ring-offset-0"
              style="accent-color: var(--sg-navy-700)">
            Remember me on this device
          </label>

          <button type="submit" id="login-submit" class="sg-btn sg-btn-primary w-full !py-3 text-sm font-semibold mt-2">
            Sign in to dashboard
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </button>
        </form>

        <div class="mt-8 pt-6 border-t" style="border-color:var(--sg-border)">
          <p class="text-center text-sm" style="color:var(--sg-text-muted)">
            New student at CKC?
            <a href="{{ route('register') }}" class="font-semibold ml-1" style="color:var(--sg-navy-600)">
              Create a student account
            </a>
          </p>
        </div>
      </div>
    </main>
  </div>

  <div id="toast-container"></div>
  <script src="/js/app.js"></script>
  <script>if (window.SG && SG.renderIcons) SG.renderIcons();</script>
</body>

</html>