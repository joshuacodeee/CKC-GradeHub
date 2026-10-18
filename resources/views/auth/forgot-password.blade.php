<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Reset Password · CKC GradeHub</title>
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

    .auth-grid {
      background-image:
        linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
      background-size: 32px 32px;
    }

    input::-ms-reveal,
    input::-ms-clear {
      display: none;
    }

    .auth-brand-logo {
      width: 56px;
      height: 56px;
      border-radius: 9999px;
      object-fit: cover;
      object-position: center;
      background: #fff;
      border: 1px solid rgba(255, 255, 255, 0.12);
      box-shadow: 0 10px 24px rgba(11, 26, 51, 0.18);
    }
  </style>
</head>

<body class="antialiased" style="background:var(--sg-bg)">

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
        <img src="{{ asset('logo/gradehub.jpg') }}" alt="CKC GradeHub" class="auth-brand-logo">
        <span class="leading-tight">
          <strong class="font-display block text-base">CKC GradeHub</strong>
          <small class="text-xs text-white/60">Christ the King College</small>
        </span>
      </a>

      <div class="relative max-w-lg">
        <div class="flex items-center gap-3 mb-6">
          <span class="auth-rule"></span>
          <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color:var(--sg-brass-500)">
            Account recovery
          </p>
        </div>
        <h1 class="auth-display text-4xl xl:text-5xl font-bold mb-4">
          Forgot your password?<br>
          <span style="color:var(--sg-brass-500)">We've got you.</span>
        </h1>
        <p class="text-white/65 text-base leading-relaxed max-w-md">
          Enter the email address on your CKC GradeHub account and we'll send a
          one-time link that lets you pick a new password.
        </p>
      </div>

      <div class="relative flex items-center gap-6 text-white/40 text-xs">
        <span>© {{ date('Y') }} Christ the King College</span>
        <span class="w-px h-3" style="background:rgba(255,255,255,0.15)"></span>
        <span>Gingoog City · S.Y. {{ date('Y') }}–{{ date('Y') + 1 }}</span>
      </div>
    </aside>

    {{-- Right: form panel --}}
    <main class="flex items-center justify-center p-6 sm:p-10 relative">
      <div class="w-full max-w-md">

        {{-- Top nav row (mobile brand + back link) --}}
        <div class="flex items-center justify-between mb-10">
          <div class="lg:hidden flex items-center gap-3">
            <img src="{{ asset('logo/gradehub.jpg') }}" alt="CKC GradeHub" class="auth-brand-logo w-12 h-12">
            <div class="leading-tight">
              <p class="font-display font-bold text-base" style="color:var(--sg-text)">CKC GradeHub</p>
              <p class="text-xs" style="color:var(--sg-text-muted)">Christ the King College</p>
            </div>
          </div>
          <a href="{{ route('login') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium hover:opacity-80 ml-auto"
            style="color:var(--sg-text-muted)">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to sign in
          </a>
        </div>

        {{-- Heading --}}
        <div class="mb-8">
          <p class="text-xs font-semibold uppercase tracking-[0.22em] mb-2" style="color:var(--sg-brass-600)">
            Reset password
          </p>
          <h2 class="auth-display text-3xl sm:text-4xl font-bold" style="color:var(--sg-text)">
            Send a reset link.
          </h2>
          <p class="text-sm mt-2" style="color:var(--sg-text-muted)">
            Use the email address tied to your CKC GradeHub account.
          </p>
        </div>

        {{-- Status / success banner --}}
        @if (session('status'))
          <div class="mb-5 rounded-xl p-3.5 flex items-start gap-3 border"
            style="background:var(--sg-success-bg); border-color:var(--sg-success) !important;">
            <i data-lucide="circle-check" class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:var(--sg-success)"></i>
            <p class="text-sm" style="color:var(--sg-text)">{{ session('status') }}</p>
          </div>
        @endif

        {{-- Errors --}}
        @if ($errors->any())
          <div class="mb-5 rounded-xl p-3.5 flex items-start gap-3 border"
            style="background:var(--sg-danger-bg); border-color:var(--sg-danger) !important;">
            <i data-lucide="circle-x" class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:var(--sg-danger)"></i>
            <p class="text-sm" style="color:var(--sg-text)">
              {{ $errors->first() ?: 'Please check the form and try again.' }}
            </p>
          </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5" novalidate>
          @csrf

          <div>
            <label for="email" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">
              Email address
            </label>
            <div class="relative">
              <i data-lucide="mail" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                style="color:var(--sg-text-muted)"></i>
              <input type="email" id="email" name="email" required autocomplete="email"
                value="{{ old('email') }}" placeholder="you@ckcgingoog.edu.ph"
                class="sg-input !pl-10 !py-3">
            </div>
            @error('email')
              <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
              </p>
            @enderror
          </div>

          <button type="submit" class="sg-btn sg-btn-primary w-full !py-3 text-sm font-semibold mt-2">
            Send reset link
            <i data-lucide="send" class="w-4 h-4"></i>
          </button>
        </form>

        <div class="mt-8 pt-6 border-t" style="border-color:var(--sg-border)">
          <p class="text-center text-sm" style="color:var(--sg-text-muted)">
            Remembered it after all?
            <a href="{{ route('login') }}" class="font-semibold ml-1" style="color:var(--sg-navy-600)">
              Sign in instead
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
