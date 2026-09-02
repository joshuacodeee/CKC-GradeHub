<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Student Account · CKC GradeHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
        rel="stylesheet">
    <script src="{{ asset('css/tailwind.css') }}"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
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

        .auth-step {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: var(--sg-text-muted);
        }

        .auth-step .num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 999px;
            font-size: 11px;
            background: var(--sg-navy-50);
            color: var(--sg-navy-700);
            font-variant-numeric: tabular-nums;
        }

        .auth-step-divider {
            flex: 1;
            height: 1px;
            background: var(--sg-border);
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

        .pw-strength-bar {
            height: 4px;
            border-radius: 999px;
            background: var(--sg-border);
            overflow: hidden;
        }

        .pw-strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 999px;
            transition: width .2s ease, background-color .2s ease;
        }
    </style>
</head>

<body class="antialiased" style="background:var(--sg-bg)">

    @php
        $schoolYearName = $authSchoolYear?->name ?? 'S.Y. ' . date('Y') . '–' . (date('Y') + 1);
        $weightedAverage = $authWeightedAverage !== null ? round((float) $authWeightedAverage, 1) : null;
        $topStudent = $authTopStudent;
        $topInitials = $topStudent
            ? collect(explode(' ', $topStudent['name']))->filter()->take(2)
                ->map(fn($p) => strtoupper(substr($p, 0, 1)))->join('')
            : null;
    @endphp

    <div class="min-h-screen grid lg:grid-cols-[0.85fr_1.15fr]">

        {{-- Left: brand / illustration panel --}}
        <aside
            class="hidden lg:flex flex-col justify-between p-12 xl:p-16 relative overflow-hidden auth-panel-bg text-white">
            <div class="absolute inset-0 auth-grid opacity-40 pointer-events-none"></div>

            <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full opacity-20 pointer-events-none"
                style="background: var(--sg-brass-500); filter: blur(40px);"></div>
            <div class="absolute -left-32 -bottom-32 w-[28rem] h-[28rem] rounded-full opacity-20 pointer-events-none"
                style="background: var(--sg-navy-500); filter: blur(40px);"></div>

            <a href="{{ route('landing') }}" class="relative flex items-center gap-3 group">
                <span
                    class="w-12 h-12 rounded-xl flex items-center justify-center font-display font-bold text-white tracking-wider"
                    style="background:linear-gradient(140deg,var(--sg-navy-700),var(--sg-navy-900)); border:1px solid rgba(255,255,255,0.10)">CKC</span>
                <span class="leading-tight">
                    <strong class="font-display block text-base">GradeHub</strong>
                    <small class="text-xs text-white/60">Christ the King College</small>
                </span>
            </a>

            <div class="relative max-w-md">
                <div class="flex items-center gap-3 mb-6">
                    <span class="auth-rule"></span>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color:var(--sg-brass-500)">
                        Student access
                    </p>
                </div>
                <h1 class="auth-display text-4xl xl:text-5xl font-bold mb-4">
                    Start your academic<br>
                    <span style="color:var(--sg-brass-500)">record in one place.</span>
                </h1>
                <p class="text-white/65 text-base leading-relaxed max-w-md">
                    Create your account with your official student ID, then sign in to your personal
                    CKC dashboard to follow your progress through the term.
                </p>

                {{-- Live stats card --}}
                <div class="relative mt-12">
                    <div class="auth-mock-card rounded-2xl p-5 shadow-2xl auth-float-a">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-xs uppercase tracking-wider text-white/50">Join CKC GradeHub</p>
                            <span class="text-[10px] font-semibold uppercase tracking-wider"
                                style="color:var(--sg-brass-500)">{{ $schoolYearName }}</span>
                        </div>
                        <div class="space-y-2.5">
                            <div class="auth-mock-row rounded-lg p-3 flex items-center justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-md flex items-center justify-center flex-shrink-0"
                                        style="background:rgba(200,149,47,0.15)">
                                        <i data-lucide="users" class="w-4 h-4" style="color:var(--sg-brass-500)"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold">Students enrolled</p>
                                        <p class="text-[11px] text-white/50">On the platform</p>
                                    </div>
                                </div>
                                <p class="font-display font-bold text-lg">{{ number_format($authStudentCount) }}</p>
                            </div>
                            <div class="auth-mock-row rounded-lg p-3 flex items-center justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-md flex items-center justify-center flex-shrink-0"
                                        style="background:rgba(37,99,163,0.30)">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4" style="color:#9ec0e8"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold">Active sections</p>
                                        <p class="text-[11px] text-white/50">Currently in session</p>
                                    </div>
                                </div>
                                <p class="font-display font-bold text-lg">{{ number_format($authSectionCount) }}</p>
                            </div>
                            <div class="auth-mock-row rounded-lg p-3 flex items-center justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-md flex items-center justify-center flex-shrink-0"
                                        style="background:rgba(26,138,83,0.20)">
                                        <i data-lucide="book-open" class="w-4 h-4" style="color:#5fc593"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold">Subjects offered</p>
                                        <p class="text-[11px] text-white/50">Across the curriculum</p>
                                    </div>
                                </div>
                                <p class="font-display font-bold text-lg">{{ number_format($authSubjectCount) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Floating chip: top student OR weighted average --}}
                    @if ($topStudent)
                        <div
                            class="auth-mock-card absolute -right-6 -bottom-6 rounded-xl px-4 py-3 shadow-xl auth-float-b max-w-[14rem]">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-display font-bold text-[11px] flex-shrink-0"
                                    style="background:linear-gradient(140deg,var(--sg-brass-500),var(--sg-brass-600)); color:var(--sg-navy-900)">
                                    {{ $topInitials }}
                                </div>
                                <div class="leading-tight min-w-0">
                                    <p class="text-xs font-semibold truncate">Top student</p>
                                    <p class="text-[10px] text-white/50 truncate">
                                        {{ $topStudent['name'] }} · {{ number_format($topStudent['average'], 1) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif ($weightedAverage !== null)
                        <div class="auth-mock-card absolute -right-6 -bottom-6 rounded-xl px-4 py-3 shadow-xl auth-float-b">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                    style="background:rgba(26,138,83,0.20)">
                                    <i data-lucide="trending-up" class="w-4 h-4" style="color:#5fc593"></i>
                                </div>
                                <div class="leading-tight">
                                    <p class="text-xs font-semibold">Term GWA</p>
                                    <p class="text-[10px] text-white/50">{{ number_format($weightedAverage, 1) }} weighted
                                    </p>
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

        {{-- Right: register form --}}
        <main class="flex items-start lg:items-center justify-center p-6 sm:p-10 relative">
            <div class="w-full max-w-2xl">

                {{-- Top nav row: mobile brand + back link (no longer absolute, no longer overlapping) --}}
                <div class="flex items-center justify-between mb-10">
                    <div class="lg:hidden flex items-center gap-3">
                        <span
                            class="w-11 h-11 rounded-xl flex items-center justify-center font-display font-bold text-white"
                            style="background:linear-gradient(140deg,var(--sg-navy-700),var(--sg-navy-900))">CKC</span>
                        <div class="leading-tight">
                            <p class="font-display font-bold text-base" style="color:var(--sg-text)">GradeHub</p>
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
                        Create account</p>
                    <h2 class="auth-display text-3xl sm:text-4xl font-bold" style="color:var(--sg-text)">
                        Set up your student profile.
                    </h2>
                    <p class="text-sm mt-2" style="color:var(--sg-text-muted)">
                        Use your official CKC details so we can match your account to your section.
                    </p>
                </div>

                {{-- Session errors --}}
                @if ($errors->any())
                    <div class="mb-5 rounded-xl p-3.5 flex items-start gap-3 border"
                        style="background:var(--sg-danger-bg); border-color:var(--sg-danger) !important;">
                        <i data-lucide="circle-x" class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:var(--sg-danger)"></i>
                        <div class="text-sm" style="color:var(--sg-text)">
                            <p class="font-semibold">Please fix the following:</p>
                            <ul class="mt-1 list-disc list-inside text-xs space-y-0.5" style="color:var(--sg-text-muted)">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-7" novalidate>
                    @csrf

                    {{-- Section: Identity --}}
                    <fieldset>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="auth-step">
                                <span class="num">1</span> Identity
                            </span>
                            <span class="auth-step-divider"></span>
                        </div>
                        <div class="space-y-4">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="firstname" class="block text-sm font-medium mb-1.5"
                                        style="color:var(--sg-text)">First name</label>
                                    <div class="relative">
                                        <i data-lucide="user"
                                            class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                                            style="color:var(--sg-text-muted)"></i>
                                        <input id="firstname" name="firstname" value="{{ old('firstname') }}" required
                                            autocomplete="given-name"
                                            class="sg-input !pl-10 !py-2.5" placeholder="Juan">
                                    </div>
                                    @error('firstname')
                                        <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                                            <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="middlename" class="block text-sm font-medium mb-1.5"
                                        style="color:var(--sg-text)">
                                        Middle name
                                        <span class="font-normal ml-1" style="color:var(--sg-text-muted)">(optional)</span>
                                    </label>
                                    <div class="relative">
                                        <i data-lucide="user"
                                            class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                                            style="color:var(--sg-text-muted)"></i>
                                        <input id="middlename" name="middlename" value="{{ old('middlename') }}"
                                            autocomplete="additional-name"
                                            class="sg-input !pl-10 !py-2.5" placeholder="Santos">
                                    </div>
                                    @error('middlename')
                                        <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                                            <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="lastname" class="block text-sm font-medium mb-1.5"
                                        style="color:var(--sg-text)">Last name</label>
                                    <div class="relative">
                                        <i data-lucide="user-round"
                                            class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                                            style="color:var(--sg-text-muted)"></i>
                                        <input id="lastname" name="lastname" value="{{ old('lastname') }}" required
                                            autocomplete="family-name"
                                            class="sg-input !pl-10 !py-2.5" placeholder="Dela Cruz">
                                    </div>
                                    @error('lastname')
                                        <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                                            <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <span class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Sex</span>
                                    <div class="flex items-center gap-4 h-[42px]">
                                        <label class="inline-flex items-center gap-2 text-sm cursor-pointer"
                                            style="color:var(--sg-text)">
                                            <input type="radio" name="sex" value="Male"
                                                @checked(old('sex') === 'Male') required>
                                            Male
                                        </label>
                                        <label class="inline-flex items-center gap-2 text-sm cursor-pointer"
                                            style="color:var(--sg-text)">
                                            <input type="radio" name="sex" value="Female"
                                                @checked(old('sex') === 'Female')>
                                            Female
                                        </label>
                                    </div>
                                    @error('sex')
                                        <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                                            <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="student_id" class="block text-sm font-medium mb-1.5"
                                        style="color:var(--sg-text)">Student ID</label>
                                    <div class="relative">
                                        <i data-lucide="hash"
                                            class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                                            style="color:var(--sg-text-muted)"></i>
                                        <input id="student_id" name="student_id" value="{{ old('student_id') }}"
                                            required class="sg-input !pl-10 !py-2.5" placeholder="CYR-NUMBER">
                                    </div>
                                    @error('student_id')
                                        <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                                            <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium mb-1.5"
                                        style="color:var(--sg-text)">Email address</label>
                                    <div class="relative">
                                        <i data-lucide="mail"
                                            class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                                            style="color:var(--sg-text-muted)"></i>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                            autocomplete="email" class="sg-input !pl-10 !py-2.5"
                                            pattern=".+@ckcgingoog\.edu\.ph" placeholder="you@ckcgingoog.edu.ph">
                                    </div>
                                    @error('email')
                                        <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                                            <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Section: Academic --}}
                    <fieldset>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="auth-step">
                                <span class="num">2</span> Academic details
                            </span>
                            <span class="auth-step-divider"></span>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label for="course" class="block text-sm font-medium mb-1.5"
                                    style="color:var(--sg-text)">Course</label>
                                <div class="relative">
                                    <i data-lucide="graduation-cap"
                                        class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                                        style="color:var(--sg-text-muted)"></i>
                                    <input id="course" name="course" value="{{ old('course') }}" required
                                        list="course-options" class="sg-input !pl-10 !py-2.5"
                                        placeholder="BS Information Technology">
                                </div>
                                <datalist id="course-options">
                                    @foreach ($courses as $course)
                                        <option value="{{ $course }}">
                                    @endforeach
                                </datalist>
                                @error('course')
                                    <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                                        <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="year_level" class="block text-sm font-medium mb-1.5"
                                        style="color:var(--sg-text)">Year level</label>
                                    <div class="relative">
                                        <i data-lucide="layers"
                                            class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                                            style="color:var(--sg-text-muted)"></i>
                                        <select id="year_level" name="year_level" required
                                            class="sg-input !pl-10 !py-2.5 appearance-none">
                                            <option value="" disabled @selected(old('year_level') === null)>Select year
                                                level</option>
                                            @foreach (['1st Year', '2nd Year', '3rd Year', '4th Year'] as $yl)
                                                <option value="{{ $yl }}" @selected(old('year_level') === $yl)>{{ $yl }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i data-lucide="chevron-down"
                                            class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                                            style="color:var(--sg-text-muted)"></i>
                                    </div>
                                    @error('year_level')
                                        <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                                            <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="block" class="block text-sm font-medium mb-1.5"
                                        style="color:var(--sg-text)">
                                        Block
                                        <span class="font-normal ml-1"
                                            style="color:var(--sg-text-muted)">(optional)</span>
                                    </label>
                                    <div class="relative">
                                        <i data-lucide="layout-grid"
                                            class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                                            style="color:var(--sg-text-muted)"></i>
                                        <select id="block" name="block" class="sg-input !pl-10 !py-2.5 appearance-none">
                                            <option value="" @selected(old('block') === null || old('block') === '')>— No
                                                block —</option>
                                            @foreach (['A', 'B', 'C', 'D', 'E'] as $b)
                                                <option value="{{ $b }}" @selected(old('block') === $b)>{{ $b }}</option>
                                            @endforeach
                                        </select>
                                        <i data-lucide="chevron-down"
                                            class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                                            style="color:var(--sg-text-muted)"></i>
                                    </div>
                                    @error('block')
                                        <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                                            <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Irregular student toggle --}}
                            <label class="flex items-start gap-3 px-4 py-3 rounded-lg border cursor-pointer"
                                style="border-color:var(--sg-border)">
                                <input type="checkbox" name="is_irregular" value="1"
                                    @checked(old('is_irregular')) class="mt-0.5">
                                <div>
                                    <p class="text-sm font-medium" style="color:var(--sg-text)">
                                        I am an irregular student
                                    </p>
                                    <p class="text-xs mt-0.5" style="color:var(--sg-text-muted)">
                                        Tick this if you are taking subjects from a different year level or section.
                                        The Registrar will review your missing subjects and assign you to the right class.
                                    </p>
                                </div>
                            </label>
                            @error('is_irregular')
                                <p class="text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                                    <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </fieldset>

                    {{-- Section: Security --}}
                    <fieldset>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="auth-step">
                                <span class="num">3</span> Security
                            </span>
                            <span class="auth-step-divider"></span>
                        </div>
                        <div class="space-y-4">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium mb-1.5"
                                        style="color:var(--sg-text)">Password</label>
                                    <div class="relative">
                                        <i data-lucide="lock"
                                            class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                                            style="color:var(--sg-text-muted)"></i>
                                        <input type="password" id="password" name="password" required minlength="8"
                                            autocomplete="new-password" class="sg-input !pl-10 !pr-11 !py-2.5"
                                            placeholder="At least 8 characters" data-password-strength>
                                        <button type="button" data-password-toggle="password"
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 inline-flex items-center justify-center w-7 h-7 rounded-md hover:bg-navy-50"
                                            style="color:var(--sg-text-muted)" aria-label="Toggle password visibility">
                                            <i data-lucide="eye" class="w-4 h-4" data-eye-open></i>
                                            <i data-lucide="eye-off" class="w-4 h-4 hidden" data-eye-closed></i>
                                        </button>
                                    </div>
                                    {{-- Strength meter --}}
                                    <div class="mt-2 flex items-center gap-2">
                                        <div class="pw-strength-bar flex-1">
                                            <div class="pw-strength-fill" data-pw-fill></div>
                                        </div>
                                        <span class="text-[11px] font-medium" data-pw-label
                                            style="color:var(--sg-text-muted)">—</span>
                                    </div>
                                    <p class="mt-1 text-[11px]" style="color:var(--sg-text-muted)">
                                        Use 8+ characters with a mix of letters, numbers, and symbols.
                                    </p>
                                    @error('password')
                                        <p class="mt-1.5 text-xs flex items-center gap-1.5" style="color:var(--sg-danger)">
                                            <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium mb-1.5"
                                        style="color:var(--sg-text)">Confirm password</label>
                                    <div class="relative">
                                        <i data-lucide="lock"
                                            class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                                            style="color:var(--sg-text-muted)"></i>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            required autocomplete="new-password" class="sg-input !pl-10 !pr-11 !py-2.5"
                                            placeholder="Repeat your password" data-password-match="password">
                                        <button type="button" data-password-toggle="password_confirmation"
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 inline-flex items-center justify-center w-7 h-7 rounded-md hover:bg-navy-50"
                                            style="color:var(--sg-text-muted)" aria-label="Toggle password visibility">
                                            <i data-lucide="eye" class="w-4 h-4" data-eye-open></i>
                                            <i data-lucide="eye-off" class="w-4 h-4 hidden" data-eye-closed></i>
                                        </button>
                                    </div>
                                    <p class="mt-1.5 text-[11px] flex items-center gap-1.5" data-match-msg
                                        style="color:var(--sg-text-muted)">
                                        Re-enter your password to confirm.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <button type="submit" class="sg-btn w-full !py-3 text-sm font-semibold mt-2"
                        style="background:var(--sg-brass-500); color:var(--sg-navy-900)">
                        Create my account
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t" style="border-color:var(--sg-border)">
                    <p class="text-center text-sm" style="color:var(--sg-text-muted)">
                        Already have an account?
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
    <script>
        (function () {
            const pw = document.querySelector('[data-password-strength]');
            const fill = document.querySelector('[data-pw-fill]');
            const label = document.querySelector('[data-pw-label]');
            const score = (v) => {
                if (!v) return 0;
                let s = 0;
                if (v.length >= 8) s++;
                if (v.length >= 12) s++;
                if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++;
                if (/\d/.test(v)) s++;
                if (/[^A-Za-z0-9]/.test(v)) s++;
                return Math.min(s, 4);
            };
            const updatePw = () => {
                const v = pw.value;
                const s = score(v);
                const pct = v ? (s / 4) * 100 : 0;
                fill.style.width = pct + '%';
                const levels = [
                    { color: 'var(--sg-border)', text: '—' },
                    { color: 'var(--sg-danger)', text: 'Weak' },
                    { color: 'var(--sg-warning)', text: 'Fair' },
                    { color: 'var(--sg-info)', text: 'Good' },
                    { color: 'var(--sg-success)', text: 'Strong' },
                ];
                const level = v ? levels[s] : levels[0];
                fill.style.backgroundColor = level.color;
                label.textContent = level.text;
                label.style.color = level.color;
            };
            pw.addEventListener('input', updatePw);

            const confirm = document.querySelector('[data-password-match]');
            const matchMsg = document.querySelector('[data-match-msg]');
            const updateMatch = () => {
                if (!confirm.value) {
                    matchMsg.innerHTML = 'Re-enter your password to confirm.';
                    matchMsg.style.color = 'var(--sg-text-muted)';
                    return;
                }
                if (confirm.value === pw.value) {
                    matchMsg.innerHTML = '<i data-lucide="circle-check" class="w-3.5 h-3.5"></i> Passwords match.';
                    matchMsg.style.color = 'var(--sg-success)';
                } else {
                    matchMsg.innerHTML = '<i data-lucide="circle-x" class="w-3.5 h-3.5"></i> Passwords do not match.';
                    matchMsg.style.color = 'var(--sg-danger)';
                }
                if (window.SG && SG.renderIcons) SG.renderIcons();
            };
            confirm.addEventListener('input', updateMatch);
            pw.addEventListener('input', updateMatch);
        })();
    </script>
</body>

</html>