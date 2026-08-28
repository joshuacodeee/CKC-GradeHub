@extends('layouts.app', ['role' => 'student', 'active' => 'profile', 'title' => 'My Profile'])

@php
  $user = $user ?? auth()->user();
  $userName = $user?->name ?? 'Account';
  $userEmail = $user?->email ?? 'student@example.com';
  $studentID = $user?->student_id ?? 'CYR-NUM';
  $userContactNumber = $user?->phone ?? '—';
  $userInitials = collect(explode(' ', $userName))->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->join('');
  $course = $user?->course ?? '—';
  $yearLevel = $user?->year_level ?? '1st Year';
  $sectionName = $section?->name ?? '—';
  $gwaDisplay = $gwa !== null ? $gwa : '—';
  $studentIdBadge = $studentID;
@endphp

@section('content')
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="sg-card p-6 text-center h-fit">
      <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center text-white text-2xl font-bold"
        style="background:var(--sg-navy-700)" data-current-user-initials>{{ $userInitials }}</div>
      <h3 class="font-display font-semibold text-lg mt-4" style="color:var(--sg-text)" data-current-user-name>
        {{ $userName }}
      </h3>
      <p class="text-sm" style="color:var(--sg-text-muted)">{{ $studentIdBadge }} · {{ $course }} {{ $yearLevel }}</p>
      <span class="sg-badge sg-badge-success mt-3 inline-flex">Active</span>
      <div class="mt-5 pt-5 border-t space-y-2 text-left text-sm" style="border-color:var(--sg-border)">
        <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="mail" class="w-4 h-4"></i>
          {{ $userEmail }}
        </p>
        <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="phone" class="w-4 h-4"></i>
          {{ $userContactNumber }}</p>
        <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="map-pin"
            class="w-4 h-4"></i> Imus, Cavite, Philippines</p>
      </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
      <div class="sg-card p-6 space-y-4">
        <h3 class="font-display font-semibold" style="color:var(--sg-text)">Personal Information</h3>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Full Name</label><input
              type="text" value="{{ $userName }}" class="sg-input"></div>
          <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Student ID</label><input
              type="text" value="{{ $studentID }}" class="sg-input" disabled></div>
          <div class="col-span-2"><label class="block text-sm font-medium mb-1.5"
              style="color:var(--sg-text)">Email</label><input type="email" value="{{ $userEmail }}" class="sg-input">
          </div>
          <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Contact Number</label><input
              type="text" value="{{ $userContactNumber }}" class="sg-input"></div>
          <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Address</label><input
              type="text" value="Imus, Cavite, Philippines" class="sg-input"></div>
        </div>
        <div class="pt-2 flex justify-end">
          <button class="sg-btn sg-btn-primary" data-toast="Profile updated successfully."><i data-lucide="check"
              class="w-4 h-4"></i> Save Changes</button>
        </div>
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
