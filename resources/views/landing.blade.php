<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CKC GradeHub · Christ the King College</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo/gradehub.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo/gradehub.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">
    <script src="{{ asset('css/tailwind.css') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script>document.documentElement.classList.add('js');</script>
    <style>
        /* ── Hero: campus photo under a navy wash, like the school's banner ── */
        .landing-hero {
            position: relative;
            isolation: isolate;
            background-color: var(--sg-navy-900);
            overflow: hidden;
        }

        .landing-hero::after {
            /* thin brass rule along the bottom edge */
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: var(--sg-brass-500);
        }

        .landing-rule {
            width: 48px;
            height: 3px;
            background: var(--sg-brass-500);
            display: inline-block;
        }

        .landing-title {
            font-family: 'Space Grotesk', 'DM Sans', ui-sans-serif, system-ui, sans-serif;
            letter-spacing: -0.02em;
            line-height: 1.04;
        }

        .landing-grade-card {
            background: var(--sg-surface);
            color: var(--sg-text);
            border: 1px solid var(--sg-border);
            border-top: 4px solid var(--sg-brass-500);
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, .55);
        }

        .landing-grade-pill {
            font-variant-numeric: tabular-nums;
        }

        .landing-nav a {
            position: relative;
            padding: .5rem .75rem;
            font-size: .875rem;
            font-weight: 600;
            color: var(--sg-text);
            white-space: nowrap;
        }

        .landing-nav a::after {
            content: "";
            position: absolute;
            left: .75rem;
            right: .75rem;
            bottom: 2px;
            height: 2px;
            background: var(--sg-brass-500);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .2s ease;
        }

        .landing-nav a:hover::after {
            transform: scaleX(1);
        }

        .landing-why-card {
            background: var(--sg-surface);
            border: 1px solid var(--sg-border);
            border-radius: 1rem;
            padding: 1.75rem;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .landing-why-card:hover {
            transform: translateY(-3px);
            border-color: var(--sg-brass-500);
            box-shadow: 0 18px 34px -18px rgba(11, 26, 51, .35);
        }

        .landing-photo {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            aspect-ratio: 3 / 2;
            background: var(--sg-navy-50);
        }

        .landing-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .5s ease;
        }

        .landing-photo:hover img {
            transform: scale(1.06);
        }
        /* ── Mobile safety ── */
        html { scroll-behavior: smooth; }
        body { overflow-x: hidden; }
        section[id] { scroll-margin-top: 80px; }
        .landing-mobile-nav a {
            display: block;
            padding: .75rem 0;
            font-size: .95rem;
            font-weight: 600;
            border-bottom: 1px solid var(--sg-border);
        }
        .landing-mobile-nav a:last-child { border-bottom: 0; }

        /* ── Animation ── */
        @keyframes landing-fade-up {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: none; }
        }
        @keyframes landing-float {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }
        @keyframes landing-grow {
            from { transform: scaleX(0); }
            to   { transform: scaleX(1); }
        }
        @keyframes landing-zoom {
            from { transform: scale(1); }
            to   { transform: scale(1.08); }
        }

        .hero-anim {
            opacity: 0;
            animation: landing-fade-up .8s cubic-bezier(.2, .7, .2, 1) forwards;
            animation-delay: var(--d, 0ms);
        }

        .landing-hero::before {
            /* campus photo under a navy wash, with a slow drift */
            content: "";
            position: absolute;
            inset: -80px 0;
            z-index: -1;
            background-image:
                linear-gradient(105deg, rgba(11, 26, 51, .95) 0%, rgba(11, 26, 51, .84) 48%, rgba(11, 26, 51, .58) 100%),
                url('https://ckcgingoog.edu.ph/wp-content/uploads/2023/10/IMG_0456-scaled.jpg');
            background-size: cover;
            background-position: center calc(50% + var(--py, 0px));
            background-repeat: no-repeat;
            animation: landing-zoom 24s ease-in-out infinite alternate;
        }

        .landing-float { animation: landing-float 5s ease-in-out infinite; }
        .landing-float-b { animation-delay: -2.5s; }

        .bar-grow {
            transform-origin: left;
            animation: landing-grow 1.1s .6s cubic-bezier(.2, .7, .2, 1) both;
        }

        .landing-rule { transform-origin: left; animation: landing-grow .8s .1s ease-out both; }

        .js .reveal {
            opacity: 0;
            transform: translateY(26px);
            transition: opacity .7s ease, transform .7s cubic-bezier(.2, .7, .2, 1);
            transition-delay: var(--d, 0ms);
        }
        .js .reveal.is-visible { opacity: 1; transform: none; }

        .sg-btn { transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease; }
        .sg-btn:hover { transform: translateY(-2px); }


        /* ── Scroll-driven effects ── */
        #scrollProgress {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            z-index: 60;
            background: linear-gradient(90deg, var(--sg-brass-500), var(--sg-brass-600, var(--sg-brass-500)));
            transform: scaleX(0);
            transform-origin: left;
            pointer-events: none;
        }

        .landing-header { transition: box-shadow .25s ease, background-color .25s ease; }
        .landing-header.is-scrolled { box-shadow: 0 10px 28px -14px rgba(11, 26, 51, .4); }

        .landing-nav a.is-active::after { transform: scaleX(1); }

        .js .reveal-left  { transform: translateX(-42px); }
        .js .reveal-right { transform: translateX(42px); }
        .js .reveal-zoom  { transform: scale(.94) translateY(18px); }
        @media (max-width: 1023px) {
            /* side slides become simple fade-ups on stacked layouts */
            .js .reveal-left, .js .reveal-right { transform: translateY(26px); }
        }

        /* table rows cascade in once the table scrolls into view */
        .js .reveal tbody tr {
            opacity: 0;
            transform: translateX(14px);
            transition: opacity .45s ease, transform .45s ease;
            transition-delay: 450ms;
        }
        .js .reveal.is-visible tbody tr { opacity: 1; transform: none; }
        .js .reveal.is-visible tbody tr:nth-child(1)  { transition-delay: 300ms; }
        .js .reveal.is-visible tbody tr:nth-child(2)  { transition-delay: 340ms; }
        .js .reveal.is-visible tbody tr:nth-child(3)  { transition-delay: 380ms; }
        .js .reveal.is-visible tbody tr:nth-child(4)  { transition-delay: 420ms; }
        .js .reveal.is-visible tbody tr:nth-child(5)  { transition-delay: 460ms; }
        .js .reveal.is-visible tbody tr:nth-child(6)  { transition-delay: 500ms; }
        .js .reveal.is-visible tbody tr:nth-child(7)  { transition-delay: 540ms; }
        .js .reveal.is-visible tbody tr:nth-child(8)  { transition-delay: 580ms; }
        .js .reveal.is-visible tbody tr:nth-child(9)  { transition-delay: 620ms; }
        .js .reveal.is-visible tbody tr:nth-child(10) { transition-delay: 660ms; }

        .landing-top {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 40;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            background: var(--sg-navy-800);
            color: var(--sg-brass-500);
            border: 1px solid rgba(255, 255, 255, .15);
            box-shadow: 0 12px 24px -10px rgba(11, 26, 51, .55);
            opacity: 0;
            visibility: hidden;
            transform: translateY(14px);
            transition: opacity .3s ease, transform .3s ease, visibility .3s;
        }
        .landing-top.is-shown { opacity: 1; visibility: visible; transform: none; }
        .landing-top:hover { background: var(--sg-navy-700); transform: translateY(-3px); }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .hero-anim, .js .reveal, .js .reveal tbody tr { opacity: 1 !important; transform: none !important; }
            #scrollProgress { display: none; }
            html { scroll-behavior: auto; }
        }
    </style>
</head>

<body class="antialiased" style="background:var(--sg-bg); color:var(--sg-text)">

    <div id="scrollProgress" aria-hidden="true"></div>

    @php
                // Inline SVG icons (Lucide paths) — render without any JS library.
                $iconPaths = [
                    'user-check' => '<path d="m16 11 2 2 4-4"/><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>',
                    'book-open' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
                    'file-check-2' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="m9 15 2 2 4-4"/>',
                    'layout-dashboard' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>',
                    'file-bar-chart-2' => '<path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>',
                    'school' => '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
                    'graduation-cap' => '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
                    'pen-line' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
                    'settings-2' => '<path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/>',
                    'phone' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
                    'mail' => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
                    'arrow-right' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
                    'arrow-up-right' => '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
                    'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
                    'user-plus' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/>',
                    'map-pin' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
                    'arrow-up' => '<path d="m5 12 7-7 7 7"/><path d="M12 19V5"/>',
                    'menu' => '<line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/>',
                    // small accent icons shown beside each feature title
                    'shield-check' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
                    'layers' => '<path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/>',
                    'calculator' => '<rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><path d="M16 14v4M16 10h.01M12 10h.01M8 10h.01M12 14h.01M8 14h.01M12 18h.01M8 18h.01"/>',
                    'pie-chart' => '<path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/>',
                    'printer' => '<path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/>',
                    'calendar' => '<path d="M8 2v4M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>',
                ];
                $svg = fn (string $name, string $class = 'w-5 h-5', string $style = '') =>
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" style="' . $style . '" aria-hidden="true">' . ($iconPaths[$name] ?? '') . '</svg>';
    @endphp

    <!-- {{-- ─────────── Utility bar (contact + motto, as on the school site) ─────────── --}}
    <div style="background:var(--sg-navy-900); color:rgba(255,255,255,.85)">
        <div
            class="max-w-7xl mx-auto px-6 lg:px-10 py-2 flex flex-wrap items-center justify-between gap-x-6 gap-y-1 text-xs">
            <p class="hidden sm:block font-semibold uppercase tracking-[0.22em]" style="color:var(--sg-brass-500)">
                Faith · Excellence · Service
            </p>
            <div class="flex flex-wrap items-center gap-x-5 gap-y-1">
                <a href="tel:0888610149" class="inline-flex items-center gap-1.5 hover:text-white">
                    {!! $svg('phone', 'w-3.5 h-3.5', '') !!} 088 861 0149
                </a>
                <a href="mailto:ckcrvm@ckcgingoog.edu.ph" class="inline-flex items-center gap-1.5 hover:text-white">
                    {!! $svg('mail', 'w-3.5 h-3.5', '') !!} ckcrvm@ckcgingoog.edu.ph
                </a>
            </div>
        </div>
    </div> -->

    {{-- ─────────── Header ─────────── --}}
    <header class="landing-header sticky top-0 z-30 border-b"
        style="background:var(--sg-surface); border-color:var(--sg-border)">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-3 flex items-center justify-between gap-6">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <img src="{{ asset('logo/gradehub.jpg') }}" alt="CKC GradeHub"
                    class="w-10 h-10 sm:w-12 sm:h-12 object-contain rounded-full border" style="border-color:var(--sg-border)">
                <span class="leading-tight">
                    <small class="hidden sm:block text-[10px] font-semibold uppercase tracking-[0.2em]"
                        style="color:var(--sg-text-muted)">Christ the King College</small>
                    <strong class="font-display block text-lg" style="color:var(--sg-navy-800)">CKC GradeHub</strong>
                </span>
            </a>

            <nav class="landing-nav hidden lg:flex items-center">
                <a href="https://ckcgingoog.edu.ph/about-ckc/ckc-history/" target="_blank" rel="noopener">About CKC</a>
                <a href="https://ckcgingoog.edu.ph/academics/academic-programs/" target="_blank"
                    rel="noopener">Academics</a>
                <a href="https://ckcgingoog.edu.ph/admission-and-aid/how-to-enroll/" target="_blank"
                    rel="noopener">Admission</a>
                <a href="https://ckcgingoog.edu.ph/services/registrar/" target="_blank" rel="noopener">Registrar</a>
                <a href="#features">Features</a>
                <a href="#grading-scale">Grading scale</a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('login') }}" class="sg-btn sg-btn-ghost !px-3 sm:!px-4">Sign in</a>
                <a href="{{ route('register') }}" class="sg-btn sg-btn-primary !px-3 sm:!px-4 whitespace-nowrap">
                    <span class="sm:hidden">Join</span><span class="hidden sm:inline">Create account</span>
                </a>
                <button type="button" id="navToggle" class="lg:hidden p-2 rounded-lg border"
                    style="border-color:var(--sg-border)" aria-label="Menu" aria-expanded="false"
                    aria-controls="mobileNav">
                    {!! $svg('menu', 'w-5 h-5') !!}
                </button>
            </div>
        </div>
        <nav id="mobileNav" class="hidden lg:hidden border-t px-6 py-3 landing-mobile-nav"
            style="border-color:var(--sg-border); background:var(--sg-surface)">
            <a href="https://ckcgingoog.edu.ph/about-ckc/ckc-history/" target="_blank" rel="noopener">About CKC</a>
            <a href="https://ckcgingoog.edu.ph/academics/academic-programs/" target="_blank" rel="noopener">Academics</a>
            <a href="https://ckcgingoog.edu.ph/admission-and-aid/how-to-enroll/" target="_blank" rel="noopener">Admission</a>
            <a href="https://ckcgingoog.edu.ph/services/registrar/" target="_blank" rel="noopener">Registrar</a>
            <a href="#features">Features</a>
            <a href="#grading-scale">Grading scale</a>
        </nav>
    </header>

    <main>

        {{-- ─────────── Hero ─────────── --}}
        <section class="landing-hero text-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-12 pb-20 sm:pt-16 sm:pb-24 lg:pt-24 lg:pb-32">
                <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                    {{-- Left: copy --}}
                    <div class="lg:col-span-7">
                        <div class="hero-anim flex items-center gap-3 mb-6" style="--d:0ms">
                            <span class="landing-rule"></span>
                            <p class="text-xs sm:text-sm font-semibold uppercase tracking-[0.22em]"
                                style="color:var(--sg-brass-500)">
                                Student Grading Management
                            </p>
                        </div>

                        <h1 class="hero-anim landing-title text-3xl min-[400px]:text-4xl sm:text-5xl lg:text-6xl font-bold" style="--d:100ms">
                            Your grades, subjects and records —
                            <span style="color:var(--sg-brass-500)">kept in step with the term.</span>
                        </h1>

                        <p class="hero-anim mt-6 text-base sm:text-lg leading-relaxed max-w-xl" style="color:rgba(255,255,255,.78); --d:200ms">
                            CKC GradeHub is the academic workspace of Christ the King College, Gingoog City —
                            where students, teachers, and administrators keep every grade in one trusted place.
                        </p>

                        <div class="hero-anim mt-8 sm:mt-9 flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-3" style="--d:300ms">
                            <a href="{{ route('register') }}" class="sg-btn !px-6 !py-3.5 text-base justify-center w-full sm:w-auto"
                                style="background:var(--sg-brass-500); color:var(--sg-navy-900)">
                                Start as a student
                                {!! $svg('arrow-right', 'w-4 h-4', '') !!}
                            </a>
                            <a href="{{ route('login') }}" class="sg-btn !px-6 !py-3.5 text-base justify-center w-full sm:w-auto"
                                style="background:rgba(255,255,255,.10); color:#fff; border:1px solid rgba(255,255,255,.35)">
                                Access dashboard
                            </a>
                        </div>

                        <div class="hero-anim mt-8 sm:mt-10 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm" 
                            style="color:rgba(255,255,255,.75); --d:400ms">
                            <span class="inline-flex items-center gap-2">
                                {!! $svg('shield-check', 'w-4 h-4', 'color:var(--sg-brass-500)') !!}
                                School-year scoped
                            </span>
                            <span class="hidden sm:block w-px h-4" style="background:rgba(255,255,255,.3)"></span>
                            <span class="inline-flex items-center gap-2">
                                {!! $svg('users', 'w-4 h-4', 'color:var(--sg-brass-500)') !!}
                                Students · Teachers · Admin
                            </span>
                        </div>
                    </div>

                    {{-- Right: live grade snapshot card --}}
                    <div class="lg:col-span-5 hero-anim" style="--d:250ms">
                        <div class="relative">
                            <div class="landing-grade-card rounded-2xl p-6 sm:p-7">

                                {{-- Card header --}}
                                <div class="flex items-center justify-between mb-5">
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-wider"
                                            style="color:var(--sg-text-muted)">
                                            {{ $schoolYear?->name ?? 'All terms' }}
                                        </p>
                                        <p class="font-display font-semibold text-lg mt-0.5">Term snapshot</p>
                                    </div>
                                    <span class="sg-badge"
                                        style="background:var(--sg-success-bg); color:var(--sg-success);">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                                            style="background:var(--sg-success)"></span>
                                        {{ $snapshot->count() > 0 ? 'Posted' : 'Empty' }}
                                    </span>
                                </div>

                                {{-- Subject list --}}
                                <div class="divide-y" style="border-color:var(--sg-border)">
                                    @forelse ($snapshot as $s)
                                        <div class="flex items-center justify-between py-3">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold truncate">
                                                    {{ $s['code'] }}
                                                    <span style="color:var(--sg-text-muted)" class="font-normal">·
                                                        {{ $s['name'] }}</span>
                                                </p>
                                                <p class="text-xs mt-0.5" style="color:var(--sg-text-muted)">
                                                    {{ $s['standing'] }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 sm:w-20 h-1.5 rounded-full overflow-hidden"
                                                    style="background:var(--sg-border)">
                                                    <div class="h-full rounded-full bar-grow"
                                                        style="width: {{ $s['grade'] }}%; background: var(--sg-navy-500)">
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span
                                                        class="landing-grade-pill font-display font-bold text-lg w-10 block"
                                                        style="color:var(--sg-navy-700)">{{ number_format($s['grade'], 0) }}</span>
                                                    @if (!empty($s['equivalent']))
                                                        <span class="text-[10px] font-semibold uppercase tracking-wider"
                                                            style="color:var(--sg-text-muted)">
                                                            {{ number_format((float) $s['equivalent'], 1) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="py-6 text-center">
                                            <p class="text-sm" style="color:var(--sg-text-muted)">
                                                No grades posted yet for the current term.
                                            </p>
                                        </div>
                                    @endforelse
                                </div>

                                {{-- Card footer --}}
                                <div class="mt-5 pt-5 border-t flex items-center justify-between gap-3"
                                    style="border-color:var(--sg-border)">
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-wider"
                                            style="color:var(--sg-text-muted)">General weighted average</p>
                                        @php $gwaEquivalent = $snapshotAverage !== null ? \App\Support\GradingScale::equivalent((float) $snapshotAverage) : null; @endphp
                                        <p class="landing-grade-pill font-display font-bold text-2xl mt-1"
                                            style="color:var(--sg-navy-700)">
                                            {{ $snapshotAverage !== null ? number_format($snapshotAverage, 1) : '—' }}
                                        </p>
                                        @if ($gwaEquivalent !== null)
                                            <p class="text-[10px] font-semibold uppercase tracking-wider mt-0.5"
                                                style="color:var(--sg-text-muted)">
                                                Equivalent {{ number_format((float) $gwaEquivalent, 1) }}
                                            </p>
                                        @endif
                                    </div>
                                    @if ($snapshotAverage !== null && $snapshotAverage >= 90)
                                        <span class="sg-badge"
                                            style="background:var(--sg-brass-100); color:var(--sg-brass-600)">Dean's list</span>
                                    @elseif ($snapshotAverage !== null)
                                        <span class="sg-badge sg-badge-info">Term average</span>
                                    @else
                                        <span class="sg-badge"
                                            style="background:var(--sg-border); color:var(--sg-text-muted)">No data yet</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Floating chips (stacked under the card on mobile, floating on sm+) --}}
                            <div class="grid grid-cols-2 gap-3 mt-4 sm:mt-0 sm:block">
                                <div
                                    class="landing-float sg-card rounded-2xl px-3 sm:px-4 py-3 flex items-center gap-2.5 sm:gap-3 shadow-lg sm:absolute sm:-left-6 sm:top-10">
                                    <div class="w-9 h-9 flex-shrink-0 rounded-full flex items-center justify-center"
                                        style="background:var(--sg-warning-bg)">
                                        {!! $svg('file-check-2', 'w-4 h-4', 'color:var(--sg-warning)') !!}
                                    </div>
                                    <div class="leading-tight min-w-0" style="color:var(--sg-text)">
                                        <p class="text-xs font-semibold">
                                            {{ $gradesThisWeek }}
                                            {{ \Illuminate\Support\Str::plural('grade', $gradesThisWeek) }} posted
                                        </p>
                                        <p class="text-[11px]" style="color:var(--sg-text-muted)">This week</p>
                                    </div>
                                </div>

                                <div
                                    class="landing-float landing-float-b sg-card rounded-2xl px-3 sm:px-4 py-3 flex items-center gap-2.5 sm:gap-3 shadow-lg sm:absolute sm:-right-4 sm:-bottom-6">
                                    <div class="w-9 h-9 flex-shrink-0 rounded-full flex items-center justify-center"
                                        style="background:var(--sg-success-bg)">
                                        {!! $svg('user-plus', 'w-4 h-4', 'color:var(--sg-success)') !!}
                                    </div>
                                    <div class="leading-tight min-w-0" style="color:var(--sg-text)">
                                        <p class="text-xs font-semibold">{{ number_format($studentCount) }}
                                            {{ \Illuminate\Support\Str::plural('student', $studentCount) }}</p>
                                        <p class="text-[11px]" style="color:var(--sg-text-muted)">Enrolled at CKC</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ─────────── Stats band ─────────── --}}
        <section style="background:var(--sg-navy-800); color:#fff">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-8 grid grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([['3', 'User roles'], ['100%', 'School-year scoped'], ['1.0–5.0', 'CKC grading scale'], ['24/7', 'Student access']] as [$num, $label])
                    <div class="reveal lg:border-l lg:pl-6 first:border-l-0 first:pl-0" style="border-color:rgba(255,255,255,.18); --d:{{ $loop->index * 80 }}ms">
                        @php $isCount = preg_match('/^(\d+)(%?)$/', $num, $mm); @endphp
                        <p class="font-display text-3xl font-bold" style="color:var(--sg-brass-500)"
                            @if ($isCount) data-count="{{ $mm[1] }}" data-suffix="{{ $mm[2] }}" @endif>{{ $num }}</p>
                        <p class="text-xs uppercase tracking-wider mt-1" style="color:rgba(255,255,255,.72)">
                            {{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ─────────── Why GradeHub (features) ─────────── --}}
        <section id="features" class="max-w-7xl mx-auto px-6 lg:px-10 py-14 sm:py-20 lg:py-28">
            <div class="reveal text-center max-w-2xl mx-auto mb-10 sm:mb-14">
                <div class="flex items-center justify-center gap-3 mb-5">
                    <span class="landing-rule"></span>
                    <p class="text-xs sm:text-sm font-semibold uppercase tracking-[0.22em]"
                        style="color:var(--sg-brass-600)">Why GradeHub?</p>
                    <span class="landing-rule"></span>
                </div>
                <h2 class="font-display text-3xl sm:text-4xl font-bold leading-tight" style="color:var(--sg-navy-800)">
                    A single workspace for the whole grading flow.
                </h2>
                <p class="mt-4 text-base" style="color:var(--sg-text-muted)">
                    From the moment a section is created to the day a student reviews a final grade,
                    CKC GradeHub keeps every step in one focused system.
                </p>
            </div>

            @php
                $accentFor = [
                    'user-check' => 'shield-check',
                    'book-open' => 'layers',
                    'file-check-2' => 'calculator',
                    'layout-dashboard' => 'pie-chart',
                    'file-bar-chart-2' => 'printer',
                    'school' => 'calendar',
                ];
                $features = [
                    ['icon' => 'user-check', 'tone' => 'info', 'title' => 'Role-based access',
                     'body' => 'Students, teachers, and administrators each get a workspace tailored to how they work — nothing more, nothing less.'],
                    ['icon' => 'book-open', 'tone' => 'success', 'title' => 'Subjects & sections',
                     'body' => 'Manage subjects, sections, and teacher assignments from one place. Sections stay bound to a school year and semester.'],
                    ['icon' => 'file-check-2', 'tone' => 'warning', 'title' => 'Grade recording',
                     'body' => 'Enter prelim, midterm, and final grades per assignment. Final grades are computed and color-coded for quick review.'],
                    ['icon' => 'layout-dashboard', 'tone' => 'info', 'title' => 'Dashboards that match the role',
                     'body' => 'Admins see school-wide distribution. Teachers see their classes. Students see their own progress.'],
                    ['icon' => 'file-bar-chart-2', 'tone' => 'danger', 'title' => 'Reports you can read',
                     'body' => 'Generate per-section or per-student reports. Filter by school year and export in a single click.'],
                    ['icon' => 'school', 'tone' => 'success', 'title' => 'Built for CKC',
                     'body' => 'Designed around the Christ the King College academic calendar — school years, semesters, and grading terms as first-class concepts.'],
                ];
                $toneMap = [
                    'info'    => ['bg' => 'var(--sg-info-bg)',    'fg' => 'var(--sg-navy-600)'],
                    'success' => ['bg' => 'var(--sg-success-bg)', 'fg' => 'var(--sg-success)'],
                    'warning' => ['bg' => 'var(--sg-warning-bg)', 'fg' => 'var(--sg-warning)'],
                    'danger'  => ['bg' => 'var(--sg-danger-bg)',  'fg' => 'var(--sg-danger)'],
                ];
            @endphp

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($features as $f)
                    @php $tone = $toneMap[$f['tone']] ?? $toneMap['info']; @endphp
                    <div class="landing-why-card reveal" style="--d: {{ $loop->index * 80 }}ms">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5"
                            style="background: {{ $tone['bg'] }}">
                            {!! $svg($f['icon'], 'w-6 h-6', 'color: ' . $tone['fg']) !!}
                        </div>
                        <h3 class="font-display font-semibold text-lg flex items-center gap-2"
                            style="color:var(--sg-navy-800)">
                            {{ $f['title'] }}
                            {!! $svg($accentFor[$f['icon']] ?? 'shield-check', 'w-4 h-4 opacity-60', 'color: ' . $tone['fg']) !!}
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed" style="color:var(--sg-text-muted)">{{ $f['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ─────────── Audience row ─────────── --}}
        <section class="border-y" style="border-color:var(--sg-border); background:var(--sg-surface)">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 grid lg:grid-cols-3 gap-10">
                @php
                    $audiences = [
                        ['label' => 'For students', 'title' => 'See exactly where you stand.',
                         'body' => 'Open your dashboard, see current grades, posted subjects, and a clean record of the term.',
                         'icon' => 'graduation-cap'],
                        ['label' => 'For teachers', 'title' => 'Enter grades without the friction.',
                         'body' => 'Pick a class, fill the matrix, and submit. Final grades compute and roll up to reports automatically.',
                         'icon' => 'pen-line'],
                        ['label' => 'For administrators', 'title' => 'Keep the term on track.',
                         'body' => 'School-year setup, section rosters, teacher assignments, and school-wide reports — all in one place.',
                         'icon' => 'settings-2'],
                    ];
                @endphp
                @foreach ($audiences as $a)
                    <div class="reveal flex items-start gap-4" style="--d: {{ $loop->index * 100 }}ms">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                            style="background:var(--sg-navy-800)">
                            {!! $svg($a['icon'], 'w-6 h-6', 'color:var(--sg-brass-500)') !!}
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em]"
                                style="color:var(--sg-brass-600)">{{ $a['label'] }}</p>
                            <h3 class="font-display font-semibold text-lg mt-1" style="color:var(--sg-navy-800)">
                                {{ $a['title'] }}</h3>
                            <p class="text-sm mt-2" style="color:var(--sg-text-muted)">{{ $a['body'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ─────────── Grading Scale ─────────── --}}
        <section id="grading-scale" class="max-w-7xl mx-auto px-6 lg:px-10 py-14 sm:py-20 lg:py-24">
            <div class="grid lg:grid-cols-[1fr_1.4fr] gap-10 lg:gap-14 items-start">
                <div class="reveal reveal-left">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="landing-rule"></span>
                        <p class="text-xs sm:text-sm font-semibold uppercase tracking-[0.22em]"
                            style="color:var(--sg-brass-600)">Official scale</p>
                    </div>
                    <h2 class="font-display text-3xl sm:text-4xl font-bold leading-tight"
                        style="color:var(--sg-navy-800)">
                        CKC grading equivalent, right inside the system.
                    </h2>
                    <p class="mt-4 text-base leading-relaxed" style="color:var(--sg-text-muted)">
                        The official Christ the King College grading scale is built into CKC GradeHub.
                        Every final percentage grade is automatically converted to the
                        <strong>1.0 – 5.0</strong> equivalent, and the
                        <strong>Passed / Failed</strong> remark is assigned at the
                        <strong>{{ number_format((float) $passingGrade, 0) }}%</strong> cutoff.
                    </p>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl p-4 border-l-4 border"
                            style="border-color:var(--sg-border); border-left-color:var(--sg-success); background:var(--sg-surface)">
                            <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-text-muted)">
                                Passing cutoff</p>
                            <p class="font-display text-2xl font-bold mt-1" style="color:var(--sg-success)">
                                {{ number_format((float) $passingGrade, 0) }}%
                            </p>
                            <p class="text-[11px] mt-0.5" style="color:var(--sg-text-muted)">Equivalent 3.0</p>
                        </div>
                        <div class="rounded-2xl p-4 border-l-4 border"
                            style="border-color:var(--sg-border); border-left-color:var(--sg-brass-500); background:var(--sg-surface)">
                            <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-text-muted)">
                                Highest honors</p>
                            <p class="font-display text-2xl font-bold mt-1" style="color:var(--sg-brass-600)">1.0</p>
                            <p class="text-[11px] mt-0.5" style="color:var(--sg-text-muted)">95 – 100%</p>
                        </div>
                    </div>
                </div>

                <div class="reveal reveal-right rounded-2xl border overflow-hidden"
                    style="border-color:var(--sg-border); background:var(--sg-surface)">
                    <div class="px-5 sm:px-6 py-4 flex items-center justify-between"
                        style="background:var(--sg-navy-800); color:#fff">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-[0.22em]"
                                style="color:var(--sg-brass-500)">CKC grading equivalent</p>
                            <p class="font-display font-semibold text-base mt-0.5">Percentage to 1.0 – 5.0</p>
                        </div>
                        <span class="text-[10px] font-semibold uppercase tracking-wider"
                            style="color:rgba(255,255,255,.7)">22 bands</span>
                    </div>
                    <div class="overflow-x-auto max-h-[480px] overflow-y-auto">
                        <table class="sg-table">
                            <thead class="sticky top-0" style="background:var(--sg-surface)">
                                <tr>
                                    <th class="text-right">Percentage</th>
                                    <th class="text-right">Equivalent</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gradingScale as $row)
                                    <tr>
                                        <td class="text-right font-semibold" style="color:var(--sg-text)">
                                            {{ $row['percentage'] }}
                                        </td>
                                        <td class="text-right font-display font-semibold"
                                            style="color:var(--sg-navy-700)">
                                            {{ $row['equivalent'] }}
                                        </td>
                                        <td>
                                            <span class="sg-badge sg-badge-{{ $row['tone'] }}">{{ $row['status'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        {{-- ─────────── Our campus (photo gallery, like "Our Facilities") ─────────── --}}
        <section class="border-t" style="border-color:var(--sg-border); background:var(--sg-surface)">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-14 sm:py-20">
                <div class="reveal flex flex-wrap items-end justify-between gap-4 mb-10">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="landing-rule"></span>
                            <p class="text-xs sm:text-sm font-semibold uppercase tracking-[0.22em]"
                                style="color:var(--sg-brass-600)">Our campus</p>
                        </div>
                        <h2 class="font-display text-3xl sm:text-4xl font-bold" style="color:var(--sg-navy-800)">
                            Life at Christ the King College
                        </h2>
                    </div>
                    <a href="https://ckcgingoog.edu.ph" target="_blank" rel="noopener"
                        class="sg-btn sg-btn-outline">
                        Visit the official website
                        {!! $svg('arrow-up-right', 'w-4 h-4', '') !!}
                    </a>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach (['IMG_0456', 'IMG_0448', 'IMG_0426', 'IMG_0350'] as $img)
                        <div class="landing-photo reveal reveal-zoom" style="--d: {{ $loop->index * 90 }}ms">
                            <img src="https://ckcgingoog.edu.ph/wp-content/uploads/2023/10/{{ $img }}-768x512.jpg"
                                alt="Christ the King College campus" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ─────────── CTA ─────────── --}}
        <section class="max-w-7xl mx-auto px-6 lg:px-10 py-14 sm:py-20 lg:py-28">
            <div class="reveal reveal-zoom rounded-2xl p-7 sm:p-14 lg:p-16 relative overflow-hidden"
                style="background:linear-gradient(140deg,var(--sg-navy-900),var(--sg-navy-700)); color:#fff; border-bottom:4px solid var(--sg-brass-500)">
                <div class="absolute -right-20 -top-20 w-72 h-72 rounded-full opacity-20"
                    style="background:radial-gradient(circle, var(--sg-brass-500), transparent 70%)"></div>

                <div class="relative max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color:var(--sg-brass-500)">
                        Faith, Excellence, Service
                    </p>
                    <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold mt-3 leading-tight">
                        Start the term with a clear view of every grade.
                    </h2>
                    <p class="mt-4 text-white/70 text-base sm:text-lg max-w-xl">
                        Sign in to your existing CKC GradeHub account, or create a new student profile
                        to follow your progress through the school year.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="sg-btn !px-6 !py-3.5 text-base justify-center w-full sm:w-auto"
                            style="background:var(--sg-brass-500); color:var(--sg-navy-900)">
                            Create student account
                            {!! $svg('arrow-right', 'w-4 h-4', '') !!}
                        </a>
                        <a href="{{ route('login') }}" class="sg-btn !px-6 !py-3.5 text-base justify-center w-full sm:w-auto"
                            style="background:rgba(255,255,255,0.10); color:#fff; border:1px solid rgba(255,255,255,0.25)">
                            Sign in
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    {{-- ─────────── Footer ─────────── --}}
    <footer style="background:var(--sg-navy-900); color:rgba(255,255,255,.75)">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-14 grid gap-10 md:grid-cols-3">
            <div class="reveal">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo/gradehub.jpg') }}" alt="CKC GradeHub"
                        class="w-12 h-12 object-contain rounded-full border" style="border-color:rgba(255,255,255,.2)">
                    <div class="leading-tight">
                        <p class="font-display font-bold text-white">CHRIST THE KING COLLEGE</p>
                        <p class="text-xs" style="color:var(--sg-brass-500)">Faith, Excellence, Service</p>
                    </div>
                </div>
                <p class="mt-4 text-sm leading-relaxed">
                    CKC GradeHub · Student Grading Management System for the Christ the King College community.
                </p>
            </div>

            <div class="reveal" style="--d:120ms">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white mb-4">Quick links</p>
                <ul class="space-y-2 text-sm">
                    <li><a class="hover:text-white" href="{{ route('login') }}">Sign in</a></li>
                    <li><a class="hover:text-white" href="{{ route('register') }}">Create student account</a></li>
                    <li><a class="hover:text-white" href="#grading-scale">Grading scale</a></li>
                    <li><a class="hover:text-white" href="https://ckcgingoog.edu.ph" target="_blank"
                            rel="noopener">Official CKC website</a></li>
                    <li><a class="hover:text-white" href="https://ckcgingoog.edu.ph/privacy-policy/" target="_blank"
                            rel="noopener">Privacy policy</a></li>
                </ul>
            </div>

            <div class="reveal" style="--d:240ms">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white mb-4">Contact</p>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-2.5">
                        {!! $svg('map-pin', 'w-4 h-4 mt-0.5 flex-shrink-0', 'color:var(--sg-brass-500)') !!}
                        National Highway, 9014 Gingoog City
                    </li>
                    <li class="flex items-center gap-2.5">
                        {!! $svg('phone', 'w-4 h-4 flex-shrink-0', 'color:var(--sg-brass-500)') !!}
                        <a class="hover:text-white" href="tel:0888610149">088 861 0149</a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        {!! $svg('mail', 'w-4 h-4 flex-shrink-0', 'color:var(--sg-brass-500)') !!}
                        <a class="hover:text-white"
                            href="mailto:ckcrvm@ckcgingoog.edu.ph">ckcrvm@ckcgingoog.edu.ph</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t" style="border-color:rgba(255,255,255,.12)">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-5 text-xs flex flex-col sm:flex-row justify-between gap-2">
                <p>© {{ date('Y') }} Christ the King College · Gingoog City</p>
                <p>CKC GradeHub</p>
            </div>
        </div>
    </footer>

    <button type="button" id="toTop" class="landing-top" aria-label="Back to top">
        {!! $svg('arrow-up', 'w-5 h-5') !!}
    </button>

    <script>
        // Lucide (optional — icons on this page are inline SVG).
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }

        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Count-up numbers (stats band)
        function countUp(el) {
            var target = parseInt(el.getAttribute('data-count'), 10);
            var suffix = el.getAttribute('data-suffix') || '';
            if (reduceMotion || isNaN(target)) return;
            var start = null, dur = 1300;
            el.textContent = '0' + suffix;
            function step(ts) {
                if (start === null) start = ts;
                var p = Math.min((ts - start) / dur, 1);
                el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))) + suffix;
                if (p < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }

        // Scroll reveal
        (function () {
            var els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) {
                els.forEach(function (e) { e.classList.add('is-visible'); });
                return;
            }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (en) {
                    if (!en.isIntersecting) return;
                    var el = en.target;
                    el.classList.add('is-visible');
                    io.unobserve(el);
                    el.querySelectorAll('[data-count]').forEach(countUp);
                    // drop the stagger delay afterwards so hover effects stay instant
                    setTimeout(function () { el.style.setProperty('--d', '0ms'); }, 1600);
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            els.forEach(function (e) { io.observe(e); });
        })();

        // Scroll-linked effects: progress bar, header shadow, hero parallax, back-to-top
        (function () {
            var bar = document.getElementById('scrollProgress');
            var header = document.querySelector('.landing-header');
            var hero = document.querySelector('.landing-hero');
            var toTop = document.getElementById('toTop');
            var ticking = false;

            function update() {
                var y = window.pageYOffset || document.documentElement.scrollTop || 0;
                var max = document.documentElement.scrollHeight - window.innerHeight;
                if (bar) bar.style.transform = 'scaleX(' + (max > 0 ? Math.min(y / max, 1) : 0) + ')';
                if (header) header.classList.toggle('is-scrolled', y > 8);
                if (toTop) toTop.classList.toggle('is-shown', y > 600);
                if (hero && !reduceMotion && y < hero.offsetHeight + 120) {
                    hero.style.setProperty('--py', Math.min(y * 0.25, 70) + 'px');
                }
                ticking = false;
            }
            window.addEventListener('scroll', function () {
                if (!ticking) { ticking = true; requestAnimationFrame(update); }
            }, { passive: true });
            window.addEventListener('resize', update);
            update();

            if (toTop) toTop.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
            });
        })();

        // Highlight the nav link of the section currently in view
        (function () {
            if (!('IntersectionObserver' in window)) return;
            var links = document.querySelectorAll('.landing-nav a[href^="#"]');
            var spy = new IntersectionObserver(function (entries) {
                entries.forEach(function (en) {
                    var id = '#' + en.target.id;
                    links.forEach(function (a) {
                        if (a.getAttribute('href') === id) a.classList.toggle('is-active', en.isIntersecting);
                    });
                });
            }, { rootMargin: '-40% 0px -50% 0px' });
            links.forEach(function (a) {
                var t = document.querySelector(a.getAttribute('href'));
                if (t) spy.observe(t);
            });
        })();

        // Mobile menu
        (function () {
            var btn = document.getElementById('navToggle');
            var nav = document.getElementById('mobileNav');
            if (!btn || !nav) return;
            btn.addEventListener('click', function () {
                var open = nav.classList.toggle('hidden') === false;
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            nav.addEventListener('click', function (e) {
                if (e.target.tagName === 'A') { nav.classList.add('hidden'); btn.setAttribute('aria-expanded', 'false'); }
            });
        })();
    </script>
</body>

</html>