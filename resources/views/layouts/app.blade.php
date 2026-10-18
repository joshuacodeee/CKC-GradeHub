<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} · {{ $schoolShort ?? 'CKC GradeHub' }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo/gradehub.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo/gradehub.jpg') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
        rel="stylesheet">

    {{-- Tailwind CSS (CDN build — no local build step required for this prototype) --}}
    <script src="{{ asset('css/tailwind.css') }}"></script>
    <!-- tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'ui-sans-serif', 'system-ui'],
            display: ['Sora', 'Inter', 'ui-sans-serif'],
          },
          colors: {
            navy: {
              50: '#f3f7fc', 100: '#e7eff8', 400: '#4f89c4', 500: '#2563a3',
              600: '#1d4d82', 700: '#163a63', 800: '#102a4c', 900: '#0b1a33',
            },
            brass: { 100: '#faf1de', 500: '#c8952f', 600: '#a9761f' },
          },
        },
      },
    }; -->
    </script>

    {{-- Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Custom design tokens & component styles --}}
    <link rel="stylesheet" href="/css/custom.css?v={{ filemtime(public_path('css/custom.css')) }}">

    @if (($role ?? '') === 'registrar')
        <style>
            @media print {
                body { display: none !important; }
            }
        </style>
    @endif

    <style>
        .sidebar-collapsed #sg-sidebar {
            width: 84px;
        }

        .sidebar-collapsed .sg-label {
            display: none;
        }

        .sidebar-collapsed .sg-brand-text {
            display: none;
        }

        .sidebar-collapsed #sg-main {
            margin-left: 84px;
        }

        #sg-main {
            margin-left: 272px;
            transition: margin-left .2s ease;
        }

        #sg-sidebar {
            width: 272px;
            transition: width .2s ease;
        }

        @media (max-width: 1023px) {
            #sg-main {
                margin-left: 0 !important;
            }

            #sg-sidebar {
                position: fixed;
                z-index: 50;
                height: 100vh;
            }
        }

        .sg-tab-active {
            background: var(--sg-navy-700);
            color: #fff !important;
        }
    </style>
    @stack('styles')
</head>

<body class="antialiased">

    <div id="sg-app-shell" class="min-h-screen">

        {{-- Mobile backdrop --}}
        <div id="sg-sidebar-backdrop" class="hidden fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

        {{-- Sidebar --}}
        @include('components.sidebar', ['role' => $role ?? 'admin', 'active' => $active ?? ''])

        {{-- Main column --}}
        <div id="sg-main">
            @include('components.navbar', ['title' => $title ?? 'Dashboard', 'role' => $role ?? 'admin'])

            <main class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto">
                @if (session('success'))
                    <div data-flash data-flash-type="success" class="sg-card p-4 mb-4 flex items-start gap-3 border"
                        style="background:var(--sg-success-bg); border-color:var(--sg-success) !important;">
                        <i data-lucide="circle-check" class="w-5 h-5 flex-shrink-0 mt-0.5"
                            style="color:var(--sg-success)"></i>
                        <p class="text-sm font-medium" style="color:var(--sg-text)">{{ session('success') }}</p>
                        <button type="button" data-flash-close class="ml-auto sg-btn sg-btn-ghost !p-1"><i
                                data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                @endif
                @if (session('error'))
                    <div data-flash data-flash-type="error" class="sg-card p-4 mb-4 flex items-start gap-3 border"
                        style="background:var(--sg-danger-bg); border-color:var(--sg-danger) !important;">
                        <i data-lucide="circle-x" class="w-5 h-5 flex-shrink-0 mt-0.5"
                            style="color:var(--sg-danger)"></i>
                        <p class="text-sm font-medium" style="color:var(--sg-text)">{{ session('error') }}</p>
                        <button type="button" data-flash-close class="ml-auto sg-btn sg-btn-ghost !p-1"><i
                                data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                @endif
                @if (session('info'))
                    <div data-flash data-flash-type="info" class="sg-card p-4 mb-4 flex items-start gap-3 border"
                        style="background:var(--sg-info-bg); border-color:var(--sg-navy-600) !important;">
                        <i data-lucide="info" class="w-5 h-5 flex-shrink-0 mt-0.5" style="color:var(--sg-navy-600)"></i>
                        <p class="text-sm font-medium" style="color:var(--sg-text)">{{ session('info') }}</p>
                        <button type="button" data-flash-close class="ml-auto sg-btn sg-btn-ghost !p-1"><i
                                data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Toast container --}}
    <div id="toast-container"></div>

    {{-- Global confirm dialog --}}
    <div id="confirm-dialog" class="hidden fixed inset-0 z-[80] sg-modal-backdrop flex items-center justify-center p-4">
        <div class="sg-modal sg-surface rounded-2xl w-full max-w-sm p-6">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                    style="background:var(--sg-danger-bg)">
                    <i data-lucide="alert-triangle" class="w-5 h-5" style="color:var(--sg-danger)"></i>
                </div>
                <div>
                    <h3 data-confirm-title class="font-display font-semibold text-base" style="color:var(--sg-text)">
                        Please
                        confirm</h3>
                    <p data-confirm-message class="text-sm mt-1" style="color:var(--sg-text-muted)"></p>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button data-confirm-no class="sg-btn sg-btn-outline">Cancel</button>
                <button data-confirm-yes class="sg-btn sg-btn-danger">Confirm</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')

</body>

</html>
