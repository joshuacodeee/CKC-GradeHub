<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Student Account · CKC GradeHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">
    <script src="{{ asset('css/tailwind.css') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>

<body class="antialiased" style="background:var(--sg-bg)">
    <div class="min-h-screen grid lg:grid-cols-[.8fr_1.2fr]">
        <aside class="hidden lg:flex flex-col justify-between p-12 text-white"
            style="background:linear-gradient(145deg,var(--sg-navy-900),var(--sg-navy-700))">
            <a href="{{ route('landing') }}" class="flex items-center gap-3"><span
                    class="w-11 h-11 rounded-xl bg-white text-center pt-3 font-display font-bold"
                    style="color:var(--sg-navy-700)">CKC</span><span><strong
                        class="font-display block">GradeHub</strong><small class="text-white/60">Christ the King
                        College</small></span></a>
            <div>
                <p class="text-sm font-bold uppercase tracking-[.18em] mb-4" style="color:var(--sg-brass-500)">Student
                    access</p>
                <h1 class="font-display text-4xl font-bold leading-tight">Start your academic record in one place.</h1>
                <p class="text-white/60 mt-5 leading-relaxed">Create your account with your official student ID, then
                    sign in to your personal CKC dashboard.</p>
            </div>
            <p class="text-xs text-white/50">Christ the King College · Gingoog City</p>
        </aside>
        <main class="flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-lg">
                <div class="lg:hidden flex items-center gap-3 mb-10"><span
                        class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold"
                        style="background:var(--sg-navy-700)">CKC</span><strong class="font-display">GradeHub</strong>
                </div>
                <a href="{{ route('login') }}" class="text-sm font-medium inline-flex items-center gap-1"
                    style="color:var(--sg-text-muted)">
                    <span aria-hidden="true">←</span> Back to sign in
                </a>
                <h2 class="font-display text-3xl font-bold mt-8 mb-2" style="color:var(--sg-text)">Create a student
                    account</h2>
                <p class="mb-8" style="color:var(--sg-text-muted)">Use your official details to get started.</p>
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1.5">Full name</label>
                        <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name"
                            class="sg-input" placeholder="Your full name">
                        @error('name')
                            <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="student_id" class="block text-sm font-medium mb-1.5">Student ID</label>
                            <input id="student_id" name="student_id" value="{{ old('student_id') }}" required
                                class="sg-input" placeholder="e.g. 2026-0001">
                            @error('student_id')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium mb-1.5">Email address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email" class="sg-input" pattern=".+@ckcgingoog\.edu\.ph"
                                placeholder="you@ckcgingoog.edu.ph">
                            @error('email')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="course" class="block text-sm font-medium mb-1.5">Course</label>
                        <input id="course" name="course" value="{{ old('course') }}" required list="course-options"
                            class="sg-input" placeholder="e.g. BS Information Technology">
                        <datalist id="course-options">
                            @foreach ($courses as $course)
                                <option value="{{ $course }}">
                            @endforeach
                        </datalist>
                        @error('course')
                            <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="year_level" class="block text-sm font-medium mb-1.5">Year level</label>
                            <select id="year_level" name="year_level" required class="sg-input">
                                <option value="" disabled @selected(old('year_level') === null)>Select year level
                                </option>
                                @foreach (['1st Year', '2nd Year', '3rd Year', '4th Year'] as $yl)
                                    <option value="{{ $yl }}" @selected(old('year_level') === $yl)>{{ $yl }}
                                    </option>
                                @endforeach
                            </select>
                            @error('year_level')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="block" class="block text-sm font-medium mb-1.5">Block <span
                                    style="color:var(--sg-text-muted); font-weight:400">(optional)</span></label>
                            <select id="block" name="block" class="sg-input">
                                <option value="" @selected(old('block') === null || old('block') === '')>— No block —</option>
                                @foreach (['A', 'B', 'C', 'D', 'E'] as $b)
                                    <option value="{{ $b }}" @selected(old('block') === $b)>{{ $b }}</option>
                                @endforeach
                            </select>
                            @error('block')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="password" class="block text-sm font-medium mb-1.5">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required minlength="8"
                                    autocomplete="new-password" class="sg-input pr-10"
                                    placeholder="At least 8 characters">
                                <button type="button" data-password-toggle="password"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2"
                                    style="color:var(--sg-text-muted)" aria-label="Toggle password visibility">
                                    <svg data-eye-open xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="hidden">
                                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                        <path
                                            d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                        <path
                                            d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                        <line x1="2" y1="2" x2="22" y2="22" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium mb-1.5">Confirm
                                password</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    autocomplete="new-password" class="sg-input pr-10"
                                    placeholder="Repeat your password">
                                <button type="button" data-password-toggle="password_confirmation"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2"
                                    style="color:var(--sg-text-muted)" aria-label="Toggle password visibility">
                                    <svg data-eye-open xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="hidden">
                                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                        <path
                                            d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                        <path
                                            d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                        <line x1="2" y1="2" x2="22" y2="22" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="sg-btn sg-btn-primary w-full !py-3">Create account</button>
                </form>
                <p class="text-center text-sm mt-6" style="color:var(--sg-text-muted)">Already registered? <a
                        href="{{ route('login') }}" class="font-semibold" style="color:var(--sg-navy-600)">Sign in</a>
                </p>
            </div>
        </main>
    </div>
</body>

</html>