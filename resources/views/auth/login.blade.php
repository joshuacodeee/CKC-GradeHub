<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In · CKC GradeHub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
    rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class', theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'ui-sans-serif'], display: ['Sora', 'Inter', 'ui-sans-serif'] },
          colors: { navy: { 50: '#f3f7fc', 100: '#e7eff8', 400: '#4f89c4', 500: '#2563a3', 600: '#1d4d82', 700: '#163a63', 800: '#102a4c', 900: '#0b1a33' }, brass: { 100: '#faf1de', 500: '#c8952f', 600: '#a9761f' } }
        }
      }
    };
  </script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="/css/custom.css">
</head>

<body class="antialiased" style="background:var(--sg-bg)">

  <div class="min-h-screen grid lg:grid-cols-2">

    {{-- Left: brand / illustration panel --}}
    <div class="hidden lg:flex flex-col justify-between p-12 relative overflow-hidden"
      style="background: linear-gradient(160deg, var(--sg-navy-900), var(--sg-navy-700));">
      <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full opacity-20"
        style="background: var(--sg-brass-500);"></div>
      <div class="absolute -left-32 bottom-0 w-80 h-80 rounded-full opacity-10 border-[40px]"
        style="border-color:#fff;"></div>

      <div class="relative flex items-center gap-3">
        <a href="/" class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-lg overflow-hidden bg-white flex items-center justify-center flex-shrink-0">

          </div>
          <div>
            <p class="font-display font-bold text-white text-lg leading-tight">Christ the King College</p>
            <p class="text-white/60 text-xs leading-tight tracking-wide">— GINGOOG CITY —</p>
          </div>
        </a>
      </div>

      <div class="relative max-w-md">
        <p class="text-sm font-semibold tracking-wide uppercase mb-3" style="color:var(--sg-brass-500)">CKC GradeHub</p>
        <h1 class="font-display text-white text-4xl font-bold leading-tight mb-4">One dashboard for admins, teachers,
          and students.</h1>
        <p class="text-white/60 text-sm leading-relaxed">Track enrollment, encode grades, and monitor academic
          performance — all in a single, streamlined workspace built for the whole school.</p>
      </div>

      <div class="relative flex items-center gap-6 text-white/50 text-xs">
        <span>© {{ date('Y') }} Christ the King College — Gingoog City</span>
        <span>·</span>
        <span>Frontend Prototype</span>
      </div>
    </div>

    {{-- Right: login form --}}
    <div class="flex items-center justify-center p-6 sm:p-10 relative">
      <a href="/" class="absolute top-6 left-6 sm:top-8 sm:left-8 inline-flex items-center gap-1.5 text-sm font-medium"
        style="color:var(--sg-text-muted)">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Home
      </a>

      <div class="w-full max-w-sm">

        <div class="lg:hidden flex items-center gap-3 mb-8">
          <div
            class="w-10 h-10 rounded-lg overflow-hidden bg-white flex items-center justify-center flex-shrink-0 border"
            style="border-color:var(--sg-border)">

          </div>
          <div>
            <p class="font-display font-bold text-sm leading-tight" style="color:var(--sg-text)">CKC GradeHub</p>
            <p class="text-xs" style="color:var(--sg-text-muted)">Christ the King College</p>
          </div>
        </div>

        <h2 class="font-display text-2xl font-bold mb-1" style="color:var(--sg-text)">Welcome back</h2>
        <p class="text-sm mb-8" style="color:var(--sg-text-muted)">Sign in to access your dashboard.</p>

        <form id="login-form" class="space-y-4" novalidate>
          <div>
            <label for="email" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email or
              Username</label>
            <div class="relative">
              <i data-lucide="mail" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2"
                style="color:var(--sg-text-muted)"></i>
              <input type="text" id="email" name="email" required autocomplete="username"
                placeholder="you@ckcgingoog.edu.ph" class="sg-input pl-9">
            </div>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="password" class="block text-sm font-medium" style="color:var(--sg-text)">Password</label>
              <a href="#" class="text-xs font-medium" style="color:var(--sg-navy-500)">Forgot password?</a>
            </div>
            <div class="relative">
              <i data-lucide="lock" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2"
                style="color:var(--sg-text-muted)"></i>
              <input type="password" id="password" name="password" required autocomplete="current-password"
                placeholder="••••••••" class="sg-input pl-9 pr-10">
              <button type="button" data-password-toggle="password" class="absolute right-2.5 top-1/2 -translate-y-1/2"
                style="color:var(--sg-text-muted)">
                <i data-lucide="eye" class="w-4 h-4"></i>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm select-none" style="color:var(--sg-text)">
              <input type="checkbox" class="rounded" style="accent-color: var(--sg-navy-600)"> Remember me
            </label>
          </div>

          <button type="submit" id="login-submit" class="sg-btn sg-btn-primary w-full !py-2.5 text-sm">
            Sign In
          </button>
        </form>

        <div class="my-6 flex items-center gap-3">
          <div class="h-px flex-1" style="background:var(--sg-border)"></div>
          <span class="text-xs" style="color:var(--sg-text-muted)">Demo Accounts</span>
          <div class="h-px flex-1" style="background:var(--sg-border)"></div>
        </div>

        <div class="grid grid-cols-3 gap-2">
          <button type="button" data-demo-login="admin@example.com"
            class="sg-btn sg-btn-outline flex-col !py-3 text-xs gap-1">
            <i data-lucide="shield-user" class="w-4 h-4"></i> Admin
          </button>
          <button type="button" data-demo-login="teacher@example.com"
            class="sg-btn sg-btn-outline flex-col !py-3 text-xs gap-1">
            <i data-lucide="graduation-cap" class="w-4 h-4"></i> Teacher
          </button>
          <button type="button" data-demo-login="student@example.com"
            class="sg-btn sg-btn-outline flex-col !py-3 text-xs gap-1">
            <i data-lucide="user" class="w-4 h-4"></i> Student
          </button>
        </div>
        <p class="text-xs text-center mt-4" style="color:var(--sg-text-muted)">Tap a role above to autofill demo
          credentials, then Sign In.</p>
      </div>
    </div>
  </div>

  <div id="toast-container"></div>
  <script src="/js/mock-data.js"></script>
  <script src="/js/app.js"></script>
  <script>if (window.SG && SG.renderIcons) SG.renderIcons();</script>
</body>

</html>