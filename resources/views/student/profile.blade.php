@extends('layouts.app', ['role' => 'student', 'active' => 'profile', 'title' => 'My Profile'])

@php
    $user = $user ?? auth()->user();
    $userName = $user?->name ?? 'Account';
    $userEmail = $user?->email ?? '';
    $studentID = $user?->student_id ?? '';
    $userInitials = collect(explode(' ', $userName))
        ->filter()
        ->take(2)
        ->map(fn($part) => strtoupper(substr($part, 0, 1)))
        ->join('');
    $course = $user?->course ?? '—';
    $yearLevel = $user?->year_level ?? '—';
    $block = $user?->block ?: '—';
    $sectionName = $section?->name ?? '—';
    $gwaDisplay = $gwa !== null ? $gwa : '—';
@endphp

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="sg-card p-6 text-center h-fit">
            <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center text-white text-2xl font-bold"
                style="background:var(--sg-navy-700)" data-current-user-initials>{{ $userInitials }}</div>
            <h3 class="font-display font-semibold text-lg mt-4" style="color:var(--sg-text)" data-current-user-name>
                {{ $userName }}
            </h3>
            <p class="text-sm" style="color:var(--sg-text-muted)">{{ $studentID }} · {{ $course }}
                {{ $yearLevel }}{{ $block !== '—' ? ' · Block ' . $block : '' }}</p>
            <span class="sg-badge sg-badge-success mt-3 inline-flex">Active</span>
            @if ($user->is_irregular)
                <span class="sg-badge sg-badge-warning mt-3 inline-flex">Irregular</span>
            @endif
            <div class="mt-5 pt-5 border-t space-y-2 text-left text-sm" style="border-color:var(--sg-border)">
                <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="mail"
                        class="w-4 h-4"></i>
                    {{ $userEmail }}
                </p>
                <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="phone"
                        class="w-4 h-4"></i>
                    {{ $user?->phone ?: 'No phone on file' }}</p>
                <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="user-round"
                        class="w-4 h-4"></i>
                    Sex: <span style="color:var(--sg-text)">{{ $user?->sex ?: '—' }}</span></p>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="sg-card p-6 space-y-4">
                <h3 class="font-display font-semibold" style="color:var(--sg-text)">Personal Information</h3>
                <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Last Name</label>
                            <input type="text" name="lastname" value="{{ old('lastname', $user?->lastname) }}" required
                                class="sg-input">
                            @error('lastname')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">First Name</label>
                            <input type="text" name="firstname" value="{{ old('firstname', $user?->firstname) }}"
                                required class="sg-input">
                            @error('firstname')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Student ID</label>
                            <input type="text" value="{{ $studentID }}" class="sg-input" disabled>
                        </div>
                        <div>
                            <span class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Sex</span>
                            <div class="flex items-center gap-4 h-[42px]">
                                <label class="inline-flex items-center gap-2 text-sm cursor-pointer"
                                    style="color:var(--sg-text)">
                                    <input type="radio" name="sex" value="Male" @checked(old('sex', $user?->sex) === 'Male')>
                                    Male
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm cursor-pointer"
                                    style="color:var(--sg-text)">
                                    <input type="radio" name="sex" value="Female" @checked(old('sex', $user?->sex) === 'Female')>
                                    Female
                                </label>
                            </div>
                            @error('sex')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email</label>
                            <input type="email" name="email" value="{{ old('email', $userEmail) }}" required
                                class="sg-input">
                            @error('email')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user?->phone) }}"
                                placeholder="e.g. 0917-555-0100" class="sg-input">
                            @error('phone')
                                <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i>
                            Save Changes</button>
                    </div>
                </form>
            </div>

            <div class="sg-card p-6 space-y-3">
                <h3 class="font-display font-semibold" style="color:var(--sg-text)">Academic Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p style="color:var(--sg-text-muted)">Course</p>
                        <p class="font-medium" style="color:var(--sg-text)">{{ $course }}</p>
                    </div>
                    <div>
                        <p style="color:var(--sg-text-muted)">Year Level</p>
                        <p class="font-medium" style="color:var(--sg-text)">{{ $yearLevel }}</p>
                    </div>
                    <div>
                        <p style="color:var(--sg-text-muted)">Block</p>
                        <p class="font-medium" style="color:var(--sg-text)">{{ $block }}</p>
                    </div>
                    <div>
                        <p style="color:var(--sg-text-muted)">Section</p>
                        <p class="font-medium" style="color:var(--sg-text)">{{ $sectionName }}</p>
                    </div>
                    <div>
                        <p style="color:var(--sg-text-muted)">Current GWA</p>
                        <p class="font-medium" style="color:var(--sg-text)">{{ $gwaDisplay }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
