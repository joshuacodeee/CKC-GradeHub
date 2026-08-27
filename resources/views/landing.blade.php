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
</head>

<body class="antialiased" style="background:var(--sg-bg); color:var(--sg-text)">
    <header class="relative z-10 flex items-center justify-between max-w-7xl mx-auto px-6 py-6 lg:px-10"><a
            href="{{ route('landing') }}" class="flex items-center gap-3"><span
                class="w-11 h-11 rounded-xl flex items-center justify-center font-display font-bold text-white"
                style="background:var(--sg-navy-700)">CKC</span><span><strong
                    class="font-display block leading-tight">GradeHub</strong><small
                    style="color:var(--sg-text-muted)">Christ the King College</small></span></a>
        <nav class="flex items-center gap-3"><a href="{{ route('login') }}" class="sg-btn sg-btn-ghost">Sign in</a><a
                href="{{ route('register') }}" class="sg-btn sg-btn-primary">Create student account</a></nav>
    </header>
    <main>
        <section class="max-w-7xl mx-auto px-6 pt-12 pb-20 lg:px-10 lg:pt-20">
            <div class="grid lg:grid-cols-[1.05fr_.95fr] gap-14 items-center">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[.18em] mb-5" style="color:var(--sg-brass-600)">
                        Student Grading Management System</p>
                    <h1 class="font-display text-5xl sm:text-6xl font-bold leading-[1.05] max-w-2xl">Your academic
                        progress, clearly in view.</h1>
                    <p class="mt-6 text-lg leading-relaxed max-w-xl" style="color:var(--sg-text-muted)">CKC GradeHub
                        brings grades, subjects, and academic records into one focused space for the Christ the King
                        College community.</p>
                    <div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('register') }}"
                            class="sg-btn sg-btn-primary !px-5 !py-3">Start as a student <span
                                aria-hidden="true">→</span></a><a href="{{ route('login') }}"
                            class="sg-btn sg-btn-outline !px-5 !py-3">Access dashboard</a></div>
                </div>
                <div class="relative rounded-3xl p-6 sm:p-8 overflow-hidden"
                    style="background:linear-gradient(145deg,var(--sg-navy-900),var(--sg-navy-700)); min-height:360px">
                    <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full border-[32px] opacity-20"
                        style="border-color:var(--sg-brass-500)"></div>
                    <div class="relative h-full flex flex-col justify-between text-white">
                        <div class="flex justify-between items-start"><span class="text-sm text-white/60">Student
                                workspace</span><span class="sg-badge"
                                style="background:var(--sg-brass-500);color:var(--sg-navy-900)">Connected</span></div>
                        <div class="mt-24">
                            <p class="text-white/60 text-sm">Everything you need for the term</p>
                            <p class="font-display text-3xl font-bold mt-2">Grades. Records. Direction.</p>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mt-10">
                            <div class="rounded-xl p-3 bg-white/10">
                                <p class="text-2xl font-bold">01</p>
                                <p class="text-xs text-white/60 mt-1">Grades</p>
                            </div>
                            <div class="rounded-xl p-3 bg-white/10">
                                <p class="text-2xl font-bold">02</p>
                                <p class="text-xs text-white/60 mt-1">Subjects</p>
                            </div>
                            <div class="rounded-xl p-3 bg-white/10">
                                <p class="text-2xl font-bold">03</p>
                                <p class="text-xs text-white/60 mt-1">Records</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="border-t" style="border-color:var(--sg-border)">
            <div class="max-w-7xl mx-auto px-6 py-10 lg:px-10 grid sm:grid-cols-3 gap-8">
                <div>
                    <h2 class="font-display font-bold">One account</h2>
                    <p class="text-sm mt-2" style="color:var(--sg-text-muted)">Create your student profile and keep
                        access in one place.</p>
                </div>
                <div>
                    <h2 class="font-display font-bold">Clear records</h2>
                    <p class="text-sm mt-2" style="color:var(--sg-text-muted)">Review your academic information whenever
                        you need it.</p>
                </div>
                <div>
                    <h2 class="font-display font-bold">Built for CKC</h2>
                    <p class="text-sm mt-2" style="color:var(--sg-text-muted)">A shared system for students, teachers,
                        and administrators.</p>
                </div>
            </div>
        </section>
    </main>
</body>

</html>