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
                <a href="{{ route('landing') }}" class="text-sm font-medium" style="color:var(--sg-text-muted)">← Back
                    to home</a>
                <h2 class="font-display text-3xl font-bold mt-8 mb-2" style="color:var(--sg-text)">Create a student
                    account</h2>
                <p class="mb-8" style="color:var(--sg-text-muted)">Use your official details to get started.</p>
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    <div><label for="name" class="block text-sm font-medium mb-1.5">Full name</label><input id="name"
                            name="name" value="{{ old('name') }}" required autocomplete="name" class="sg-input"
                            placeholder="Your full name">@error('name')
                            <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div><label for="student_id" class="block text-sm font-medium mb-1.5">Student ID</label><input
                                id="student_id" name="student_id" value="{{ old('student_id') }}" required
                                class="sg-input" placeholder="e.g. 2026-0001">@error('student_id')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="email" class="block text-sm font-medium mb-1.5">Email address</label><input
                                type="email" id="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email" class="sg-input" placeholder="you@example.com">@error('email')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div><label for="password" class="block text-sm font-medium mb-1.5">Password</label><input
                                type="password" id="password" name="password" required minlength="8"
                                autocomplete="new-password" class="sg-input"
                                placeholder="At least 8 characters">@error('password')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>@enderror
                        </div>
                        <div><label for="password_confirmation" class="block text-sm font-medium mb-1.5">Confirm
                                password</label><input type="password" id="password_confirmation"
                                name="password_confirmation" required autocomplete="new-password" class="sg-input"
                                placeholder="Repeat your password"></div>
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