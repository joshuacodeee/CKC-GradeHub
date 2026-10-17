<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CKC GradeHub · Christ the King College</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">
    <script src="{{ asset('css/tailwind.css') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        /* ── Hero: campus photo under a navy wash, like the school's banner ── */
        .landing-hero {
            position: relative;
            isolation: isolate;
            background-color: var(--sg-navy-900);
            background-image:
                linear-gradient(105deg, rgba(11, 26, 51, .95) 0%, rgba(11, 26, 51, .84) 48%, rgba(11, 26, 51, .58) 100%),
                url('https://ckcgingoog.edu.ph/wp-content/uploads/2023/10/IMG_0456-scaled.jpg');
            background-size: cover;
            background-position: center;
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
    </style>
</head>

<body class="antialiased" style="background:var(--sg-bg); color:var(--sg-text)">

    {{-- ─────────── Utility bar (contact + motto, as on the school site) ─────────── --}}
    <div style="background:var(--sg-navy-900); color:rgba(255,255,255,.85)">
        <div
            class="max-w-7xl mx-auto px-6 lg:px-10 py-2 flex flex-wrap items-center justify-between gap-x-6 gap-y-1 text-xs">
            <p class="font-semibold uppercase tracking-[0.22em]" style="color:var(--sg-brass-500)">
                Faith · Excellence · Service
            </p>
            <div class="flex flex-wrap items-center gap-x-5 gap-y-1">
                <a href="tel:0888610149" class="inline-flex items-center gap-1.5 hover:text-white">
                    <i data-lucide="phone" class="w-3.5 h-3.5"></i> 088 861 0149
                </a>
                <a href="mailto:ckcrvm@ckcgingoog.edu.ph" class="inline-flex items-center gap-1.5 hover:text-white">
                    <i data-lucide="mail" class="w-3.5 h-3.5"></i> ckcrvm@ckcgingoog.edu.ph
                </a>
            </div>
        </div>
    </div>

    {{-- ─────────── Header ─────────── --}}
    <header class="sticky top-0 z-30 border-b"
        style="background:var(--sg-surface); border-color:var(--sg-border)">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-3 flex items-center justify-between gap-6">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <img src="{{ asset('logo/gradehub.jpg') }}" alt="CKC GradeHub"
                    class="w-12 h-12 object-contain rounded-full border" style="border-color:var(--sg-border)">
                <span class="leading-tight">
                    <small class="block text-[10px] font-semibold uppercase tracking-[0.2em]"
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
                <a href="{{ route('login') }}" class="sg-btn sg-btn-ghost">Sign in</a>
                <a href="{{ route('register') }}" class="sg-btn sg-btn-primary">Create account</a>
            </div>
        </div>
    </header>

    <main>

        {{-- ─────────── Hero ─────────── --}}
        <section class="landing-hero text-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-16 pb-24 lg:pt-24 lg:pb-32">
                <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                    {{-- Left: copy --}}
                    <div class="lg:col-span-7">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="landing-rule"></span>
                            <p class="text-xs sm:text-sm font-semibold uppercase tracking-[0.22em]"
                                style="color:var(--sg-brass-500)">
                                Student Grading Management
                            </p>
                        </div>

                        <h1 class="landing-title text-4xl sm:text-5xl lg:text-6xl font-bold">
                            Your grades, subjects and records —
                            <span style="color:var(--sg-brass-500)">kept in step with the term.</span>
                        </h1>

                        <p class="mt-6 text-lg leading-relaxed max-w-xl" style="color:rgba(255,255,255,.78)">
                            CKC GradeHub is the academic workspace of Christ the King College, Gingoog City —
                            where students, teachers, and administrators keep every grade in one trusted place.
                        </p>

                        <div class="mt-9 flex flex-wrap items-center gap-3">
                            <a href="{{ route('register') }}" class="sg-btn !px-6 !py-3.5 text-base"
                                style="background:var(--sg-brass-500); color:var(--sg-navy-900)">
                                Start as a student
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('login') }}" class="sg-btn !px-6 !py-3.5 text-base"
                                style="background:rgba(255,255,255,.10); color:#fff; border:1px solid rgba(255,255,255,.35)">
                                Access dashboard
                            </a>
                        </div>

                        <div class="mt-10 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm"
                            style="color:rgba(255,255,255,.75)">
                            <span class="inline-flex items-center gap-2">
                                <i data-lucide="shield-check" class="w-4 h-4" style="color:var(--sg-brass-500)"></i>
                                School-year scoped
                            </span>
                            <span class="hidden sm:block w-px h-4" style="background:rgba(255,255,255,.3)"></span>
                            <span class="inline-flex items-center gap-2">
                                <i data-lucide="users" class="w-4 h-4" style="color:var(--sg-brass-500)"></i>
                                Students · Teachers · Admin
                            </span>
                        </div>
                    </div>

                    {{-- Right: live grade snapshot card --}}
                    <div class="lg:col-span-5">
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
                                                <div class="w-20 h-1.5 rounded-full overflow-hidden"
                                                    style="background:var(--sg-border)">
                                                    <div class="h-full rounded-full"
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

                            {{-- Floating chips --}}
                            <div
                                class="hidden sm:flex absolute -left-6 top-10 sg-card rounded-2xl px-4 py-3 items-center gap-3 shadow-lg">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center"
                                    style="background:var(--sg-warning-bg)">
                                    <i data-lucide="file-check-2" class="w-4 h-4" style="color:var(--sg-warning)"></i>
                                </div>
                                <div class="leading-tight" style="color:var(--sg-text)">
                                    <p class="text-xs font-semibold">
                                        {{ $gradesThisWeek }}
                                        {{ \Illuminate\Support\Str::plural('grade', $gradesThisWeek) }} posted
                                    </p>
                                    <p class="text-[11px]" style="color:var(--sg-text-muted)">This week</p>
                                </div>
                            </div>

                            <div
                                class="hidden sm:flex absolute -right-4 -bottom-6 sg-card rounded-2xl px-4 py-3 items-center gap-3 shadow-lg">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center"
                                    style="background:var(--sg-success-bg)">
                                    <i data-lucide="user-plus" class="w-4 h-4" style="color:var(--sg-success)"></i>
                                </div>
                                <div class="leading-tight" style="color:var(--sg-text)">
                                    <p class="text-xs font-semibold">{{ number_format($studentCount) }} students</p>
                                    <p class="text-[11px]" style="color:var(--sg-text-muted)">Enrolled at CKC</p>
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
                    <div class="lg:border-l lg:pl-6 first:border-l-0 first:pl-0" style="border-color:rgba(255,255,255,.18)">
                        <p class="font-display text-3xl font-bold" style="color:var(--sg-brass-500)">{{ $num }}</p>
                        <p class="text-xs uppercase tracking-wider mt-1" style="color:rgba(255,255,255,.72)">
                            {{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ─────────── Why GradeHub (features) ─────────── --}}
        <section id="features" class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
            <div class="text-center max-w-2xl mx-auto mb-14">
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
                    <div class="landing-why-card">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5"
                            style="background: {{ $tone['bg'] }}">
                            <i data-lucide="{{ $f['icon'] }}" class="w-6 h-6" style="color: {{ $tone['fg'] }}"></i>
                        </div>
                        <h3 class="font-display font-semibold text-lg" style="color:var(--sg-navy-800)">
                            {{ $f['title'] }}</h3>
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
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                            style="background:var(--sg-navy-800)">
                            <i data-lucide="{{ $a['icon'] }}" class="w-5 h-5" style="color:var(--sg-brass-500)"></i>
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
        <section id="grading-scale" class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-24">
            <div class="grid lg:grid-cols-[1fr_1.4fr] gap-10 lg:gap-14 items-start">
                <div>
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

                <div class="rounded-2xl border overflow-hidden"
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
            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20">
                <div class="flex flex-wrap items-end justify-between gap-4 mb-10">
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
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach (['IMG_0456', 'IMG_0448', 'IMG_0426', 'IMG_0350'] as $img)
                        <div class="landing-photo">
                            <img src="https://ckcgingoog.edu.ph/wp-content/uploads/2023/10/{{ $img }}-768x512.jpg"
                                alt="Christ the King College campus" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ─────────── CTA ─────────── --}}
        <section class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
            <div class="rounded-2xl p-10 sm:p-14 lg:p-16 relative overflow-hidden"
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
                        <a href="{{ route('register') }}" class="sg-btn !px-6 !py-3.5 text-base"
                            style="background:var(--sg-brass-500); color:var(--sg-navy-900)">
                            Create student account
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('login') }}" class="sg-btn !px-6 !py-3.5 text-base"
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
            <div>
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

            <div>
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

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white mb-4">Contact</p>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-2.5">
                        <i data-lucide="map-pin" class="w-4 h-4 mt-0.5 flex-shrink-0"
                            style="color:var(--sg-brass-500)"></i>
                        National Highway, 9014 Gingoog City
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i data-lucide="phone" class="w-4 h-4 flex-shrink-0" style="color:var(--sg-brass-500)"></i>
                        <a class="hover:text-white" href="tel:0888610149">088 861 0149</a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i data-lucide="mail" class="w-4 h-4 flex-shrink-0" style="color:var(--sg-brass-500)"></i>
                        <a class="hover:text-white"
                            href="mailto:ckcrvm@ckcgingoog.edu.ph">ckcrvm@ckcgingoog.edu.ph</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t" style="border-color:rgba(255,255,255,.12)">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-5 text-xs flex flex-col sm:flex-row justify-between gap-2">
                <p>© {{ date('Y') }} Christ the King College · Gingoog City</p>
                <p>CKC GradeHub · Student Grading Management System</p>
            </div>
        </div>
    </footer>

    <script>
        // Re-initialize lucide icons for the landing page (loaded via CDN).
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    </script>
</body>

</html>