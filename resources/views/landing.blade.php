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
        .landing-hero-bg {
            background-image:
                radial-gradient(ellipse 60% 50% at 15% 20%, rgba(200, 149, 47, 0.10), transparent 60%),
                radial-gradient(ellipse 70% 60% at 95% 90%, rgba(37, 99, 163, 0.10), transparent 60%);
        }

        .landing-rule {
            width: 56px;
            height: 2px;
            background: var(--sg-brass-500);
            display: inline-block;
        }

        .landing-hero-title {
            font-family: 'Sora', 'Inter', ui-sans-serif, system-ui, sans-serif;
            letter-spacing: -0.025em;
            line-height: 0.98;
        }

        .landing-marquee {
            mask-image: linear-gradient(to right, transparent 0, #000 8%, #000 92%, transparent 100%);
        }

        .landing-grade-card {
            background: var(--sg-surface);
            border: 1px solid var(--sg-border);
            box-shadow:
                0 1px 0 rgba(255, 255, 255, 1) inset,
                0 30px 60px -20px rgba(11, 26, 51, 0.30),
                0 12px 24px -8px rgba(11, 26, 51, 0.10);
        }

        .landing-grade-pill {
            font-variant-numeric: tabular-nums;
        }

        .landing-feature-num {
            font-family: 'Sora', 'Inter', ui-sans-serif, system-ui, sans-serif;
            font-variant-numeric: tabular-nums;
        }
    </style>
</head>

<body class="antialiased" style="background:var(--sg-bg); color:var(--sg-text)">

    {{-- ─────────── Header ─────────── --}}
    <header class="relative z-10">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-6 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('logo/gradehub.jpg') }}" alt="CKC GradeHub" class="w-14 h-14 object-contain rounded-full border border-white/10">
                <span class="leading-tight">
                    <strong class="font-display block text-base">GradeHub</strong>
                    <small class="text-xs" style="color:var(--sg-text-muted)">Christ the King College</small>
                </span>
            </a>
            <nav class="flex items-center gap-2 sm:gap-3">
                <a href="#features"
                    class="hidden sm:inline-flex sg-btn sg-btn-ghost">Features</a>
                <a href="{{ route('login') }}" class="sg-btn sg-btn-ghost">Sign in</a>
                <a href="{{ route('register') }}" class="sg-btn sg-btn-primary">Create account</a>
            </nav>
        </div>
    </header>

    <main>

        {{-- ─────────── Hero ─────────── --}}
        <section class="landing-hero-bg">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-16 pb-24 lg:pt-24 lg:pb-32">
                <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                    {{-- Left: copy --}}
                    <div class="lg:col-span-7">
                        <div class="flex items-center gap-3 mb-7">
                            <span class="landing-rule"></span>
                            <p class="text-xs sm:text-sm font-semibold uppercase tracking-[0.22em]"
                                style="color:var(--sg-brass-600)">
                                Student Grading Management
                            </p>
                        </div>

                        <h1 class="landing-hero-title text-5xl sm:text-6xl lg:text-7xl font-bold">
                            Grades, subjects,<br>
                            and records —<br>
                            <span style="color:var(--sg-navy-600)">all in one place.</span>
                        </h1>

                        <p class="mt-7 text-lg leading-relaxed max-w-xl" style="color:var(--sg-text-muted)">
                            CKC GradeHub is the academic workspace for the Christ the King College community —
                            a focused system where students, teachers, and administrators keep grades, subjects,
                            and records in step with the term.
                        </p>

                        <div class="mt-9 flex flex-wrap items-center gap-3">
                            <a href="{{ route('register') }}"
                                class="sg-btn sg-btn-primary !px-6 !py-3.5 text-base">
                                Start as a student
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('login') }}"
                                class="sg-btn sg-btn-outline !px-6 !py-3.5 text-base">
                                Access dashboard
                            </a>
                        </div>

                        <div class="mt-10 flex items-center gap-5 text-sm" style="color:var(--sg-text-muted)">
                            <span class="inline-flex items-center gap-2">
                                <i data-lucide="shield-check" class="w-4 h-4"
                                    style="color:var(--sg-success)"></i>
                                School-year scoped
                            </span>
                            <span class="w-px h-4" style="background:var(--sg-border)"></span>
                            <span class="inline-flex items-center gap-2">
                                <i data-lucide="users" class="w-4 h-4" style="color:var(--sg-navy-500)"></i>
                                Students · Teachers · Admin
                            </span>
                        </div>
                    </div>

                    {{-- Right: live grade snapshot card --}}
                    <div class="lg:col-span-5">
                        <div class="relative">
                            {{-- Soft halo behind the card --}}
                            <div class="absolute -inset-6 -z-10 rounded-[36px] opacity-70 blur-2xl"
                                style="background:radial-gradient(ellipse at center, rgba(37,99,163,0.20), transparent 70%)">
                            </div>

                            <div class="landing-grade-card rounded-3xl p-6 sm:p-7">

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
                                                    <span class="landing-grade-pill font-display font-bold text-lg w-10 block"
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

                            {{-- Floating chip --}}
                            <div
                                class="hidden sm:flex absolute -left-6 top-10 sg-card rounded-2xl px-4 py-3 items-center gap-3 shadow-lg">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center"
                                    style="background:var(--sg-warning-bg)">
                                    <i data-lucide="file-check-2" class="w-4 h-4" style="color:var(--sg-warning)"></i>
                                </div>
                                <div class="leading-tight">
                                    <p class="text-xs font-semibold">
                                        {{ $gradesThisWeek }} {{ \Illuminate\Support\Str::plural('grade', $gradesThisWeek) }} posted
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
                                <div class="leading-tight">
                                    <p class="text-xs font-semibold">{{ number_format($studentCount) }} students</p>
                                    <p class="text-[11px]" style="color:var(--sg-text-muted)">Enrolled at CKC</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ─────────── Marquee / capabilities strip ─────────── --}}
        <section class="border-y" style="border-color:var(--sg-border); background:var(--sg-surface)">
            <div class="landing-marquee overflow-hidden">
                <div
                    class="max-w-7xl mx-auto px-6 lg:px-10 py-5 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center sm:text-left">
                    <div>
                        <p class="landing-feature-num text-2xl font-bold" style="color:var(--sg-navy-700)">3</p>
                        <p class="text-xs uppercase tracking-wider mt-1"
                            style="color:var(--sg-text-muted)">User roles</p>
                    </div>
                    <div>
                        <p class="landing-feature-num text-2xl font-bold" style="color:var(--sg-navy-700)">100%</p>
                        <p class="text-xs uppercase tracking-wider mt-1"
                            style="color:var(--sg-text-muted)">School-year scoped</p>
                    </div>
                    <div>
                        <p class="landing-feature-num text-2xl font-bold" style="color:var(--sg-navy-700)">1.0–5.0</p>
                        <p class="text-xs uppercase tracking-wider mt-1"
                            style="color:var(--sg-text-muted)">CKC grading scale</p>
                    </div>
                    <div>
                        <p class="landing-feature-num text-2xl font-bold" style="color:var(--sg-navy-700)">24/7</p>
                        <p class="text-xs uppercase tracking-wider mt-1"
                            style="color:var(--sg-text-muted)">Student access</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ─────────── Features ─────────── --}}
        <section id="features" class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
            <div class="max-w-2xl mb-14">
                <div class="flex items-center gap-3 mb-5">
                    <span class="landing-rule"></span>
                    <p class="text-xs sm:text-sm font-semibold uppercase tracking-[0.22em]"
                        style="color:var(--sg-brass-600)">What's inside</p>
                </div>
                <h2 class="font-display text-3xl sm:text-4xl font-bold leading-tight">
                    A single workspace for the whole grading flow.
                </h2>
                <p class="mt-4 text-base" style="color:var(--sg-text-muted)">
                    From the moment a section is created to the day a student reviews a final grade,
                    CKC GradeHub keeps every step in one focused system.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $features = [
                        [
                            'icon'    => 'user-check',
                            'accent'  => 'shield-check',
                            'tone'    => 'info',
                            'title'   => 'Role-based access',
                            'body'    => 'Students, teachers, and administrators each get a workspace tailored to how they work — nothing more, nothing less.',
                        ],
                        [
                            'icon'    => 'book-open',
                            'accent'  => 'library',
                            'tone'    => 'success',
                            'title'   => 'Subjects & sections',
                            'body'    => 'Manage subjects, sections, and teacher assignments from one place. Sections stay bound to a school year and semester.',
                        ],
                        [
                            'icon'    => 'file-check-2',
                            'accent'  => 'calculator',
                            'tone'    => 'warning',
                            'title'   => 'Grade recording',
                            'body'    => 'Enter prelim, midterm, and final grades per assignment. Final grades are computed and color-coded for quick review.',
                        ],
                        [
                            'icon'    => 'layout-dashboard',
                            'accent'  => 'bar-chart-3',
                            'tone'    => 'info',
                            'title'   => 'Dashboards that match the role',
                            'body'    => 'Admins see school-wide distribution. Teachers see their classes. Students see their own progress.',
                        ],
                        [
                            'icon'    => 'file-bar-chart-2',
                            'accent'  => 'printer',
                            'tone'    => 'danger',
                            'title'   => 'Reports you can read',
                            'body'    => 'Generate per-section or per-student reports. Filter by school year and export in a single click.',
                        ],
                        [
                            'icon'    => 'shield',
                            'accent'  => 'school',
                            'tone'    => 'success',
                            'title'   => 'Built for CKC',
                            'body'    => 'Designed around the Christ the King College academic calendar — with school years, semesters, and grading terms as first-class concepts.',
                        ],
                    ];
                @endphp
                @foreach ($features as $i => $f)
                    @php
                        $toneMap = [
                            'info'    => ['bg' => 'var(--sg-info-bg)',    'fg' => 'var(--sg-navy-600)',  'soft' => 'rgba(37,99,163,0.10)'],
                            'success' => ['bg' => 'var(--sg-success-bg)', 'fg' => 'var(--sg-success)',  'soft' => 'rgba(26,138,83,0.10)'],
                            'warning' => ['bg' => 'var(--sg-warning-bg)', 'fg' => 'var(--sg-warning)',  'soft' => 'rgba(181,106,5,0.10)'],
                            'danger'  => ['bg' => 'var(--sg-danger-bg)',  'fg' => 'var(--sg-danger)',   'soft' => 'rgba(209,52,56,0.10)'],
                        ];
                        $tone = $toneMap[$f['tone']] ?? $toneMap['info'];
                        $step = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
                    @endphp
                    <div class="sg-card relative p-6 hover:shadow-lg sg-transition overflow-hidden group">
                        {{-- Decorative background blob --}}
                        <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full pointer-events-none sg-transition"
                            style="background: {{ $tone['soft'] }}; transform: scale(1);"></div>

                        {{-- Step number --}}
                        <span class="absolute top-4 right-5 text-[11px] font-display font-bold tracking-widest"
                            style="color:var(--sg-text-muted); opacity:.55;">
                            {{ $step }}
                        </span>

                        {{-- Primary icon tile --}}
                        <div class="relative w-14 h-14 rounded-2xl flex items-center justify-center mb-5"
                            style="background: {{ $tone['bg'] }}; border: 1px solid {{ $tone['soft'] }};">
                            <i data-lucide="{{ $f['icon'] }}" class="w-6 h-6" style="color: {{ $tone['fg'] }}"></i>
                        </div>

                        <h3 class="font-display font-semibold text-lg flex items-center gap-2"
                            style="color:var(--sg-text)">
                            {{ $f['title'] }}
                            <i data-lucide="{{ $f['accent'] }}" class="w-4 h-4 opacity-60"
                                style="color: {{ $tone['fg'] }}"></i>
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed" style="color:var(--sg-text-muted)">
                            {{ $f['body'] }}
                        </p>

                        {{-- Footer accent bar --}}
                        <div class="mt-5 h-1 rounded-full overflow-hidden"
                            style="background:var(--sg-border)">
                            <div class="h-full rounded-full sg-transition"
                                style="width: 38%; background: {{ $tone['fg'] }};"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ─────────── Audience row ─────────── --}}
        <section class="border-y" style="border-color:var(--sg-border); background:var(--sg-surface)">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 grid lg:grid-cols-3 gap-10">
                @php
                    $audiences = [
                        [
                            'label' => 'For students',
                            'title' => 'See exactly where you stand.',
                            'body'  => 'Open your dashboard, see current grades, posted subjects, and a clean record of the term.',
                            'icon'  => 'graduation-cap',
                        ],
                        [
                            'label' => 'For teachers',
                            'title' => 'Enter grades without the friction.',
                            'body'  => 'Pick a class, fill the matrix, and submit. Final grades compute and roll up to reports automatically.',
                            'icon'  => 'pen-line',
                        ],
                        [
                            'label' => 'For administrators',
                            'title' => 'Keep the term on track.',
                            'body'  => 'School-year setup, section rosters, teacher assignments, and school-wide reports — all in one place.',
                            'icon'  => 'settings-2',
                        ],
                    ];
                @endphp
                @foreach ($audiences as $a)
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                            style="background:var(--sg-navy-50)">
                            <i data-lucide="{{ $a['icon'] }}" class="w-5 h-5"
                                style="color:var(--sg-navy-600)"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em]"
                                style="color:var(--sg-brass-600)">{{ $a['label'] }}</p>
                            <h3 class="font-display font-semibold text-lg mt-1">{{ $a['title'] }}</h3>
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
                    <h2 class="font-display text-3xl sm:text-4xl font-bold leading-tight">
                        CKC grading equivalent,<br>
                        <span style="color:var(--sg-navy-600)">right inside the system.</span>
                    </h2>
                    <p class="mt-4 text-base leading-relaxed" style="color:var(--sg-text-muted)">
                        The official Christ the King College grading scale is built into CKC GradeHub.
                        Every final percentage grade is automatically converted to the
                        <strong>1.0 – 5.0</strong> equivalent, and the
                        <strong>Passed / Failed</strong> remark is assigned at the
                        <strong>{{ number_format((float) $passingGrade, 0) }}%</strong> cutoff.
                    </p>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl p-4 border" style="border-color:var(--sg-border)">
                            <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-text-muted)">Passing cutoff</p>
                            <p class="font-display text-2xl font-bold mt-1" style="color:var(--sg-success)">
                                {{ number_format((float) $passingGrade, 0) }}%
                            </p>
                            <p class="text-[11px] mt-0.5" style="color:var(--sg-text-muted)">Equivalent 3.0</p>
                        </div>
                        <div class="rounded-2xl p-4 border" style="border-color:var(--sg-border)">
                            <p class="text-[10px] uppercase tracking-wider" style="color:var(--sg-text-muted)">Highest honors</p>
                            <p class="font-display text-2xl font-bold mt-1" style="color:var(--sg-brass-600)">1.0</p>
                            <p class="text-[11px] mt-0.5" style="color:var(--sg-text-muted)">95 – 100%</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border overflow-hidden"
                    style="border-color:var(--sg-border); background:var(--sg-surface)">
                    <div class="px-5 sm:px-6 py-4 flex items-center justify-between"
                        style="border-bottom:1px solid var(--sg-border); background:var(--sg-navy-50)">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-[0.22em]"
                                style="color:var(--sg-brass-600)">CKC grading equivalent</p>
                            <p class="font-display font-semibold text-base mt-0.5" style="color:var(--sg-text)">
                                Percentage to 1.0 – 5.0
                            </p>
                        </div>
                        <span class="text-[10px] font-semibold uppercase tracking-wider"
                            style="color:var(--sg-text-muted)">22 bands</span>
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

        {{-- ─────────── CTA ─────────── --}}
        <section class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
            <div class="rounded-3xl p-10 sm:p-14 lg:p-16 relative overflow-hidden"
                style="background:linear-gradient(140deg,var(--sg-navy-900),var(--sg-navy-700)); color:#fff">
                <div class="absolute -right-20 -top-20 w-72 h-72 rounded-full opacity-20"
                    style="background:radial-gradient(circle, var(--sg-brass-500), transparent 70%)"></div>
                <div class="absolute -left-10 -bottom-10 w-72 h-72 rounded-full opacity-10"
                    style="background:radial-gradient(circle, #fff, transparent 70%)"></div>

                <div class="relative max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color:var(--sg-brass-500)">
                        Ready when you are
                    </p>
                    <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold mt-3 leading-tight">
                        Start the term with a clear view of every grade.
                    </h2>
                    <p class="mt-4 text-white/70 text-base sm:text-lg max-w-xl">
                        Sign in to your existing CKC GradeHub account, or create a new student profile
                        to follow your progress through the school year.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}"
                            class="sg-btn !px-6 !py-3.5 text-base"
                            style="background:var(--sg-brass-500); color:var(--sg-navy-900)">
                            Create student account
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('login') }}"
                            class="sg-btn !px-6 !py-3.5 text-base"
                            style="background:rgba(255,255,255,0.10); color:#fff; border:1px solid rgba(255,255,255,0.20)">
                            Sign in
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    {{-- ─────────── Footer ─────────── --}}
    <footer class="border-t" style="border-color:var(--sg-border)">
        <div
            class="max-w-7xl mx-auto px-6 lg:px-10 py-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
            <p style="color:var(--sg-text-muted)">
                © {{ date('Y') }} Christ the King College · Gingoog City
            </p>
            <p style="color:var(--sg-text-muted)">
                CKC GradeHub · Student Grading Management System
            </p>
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
