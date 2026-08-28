@extends('layouts.app', ['role' => 'admin', 'active' => 'profile', 'title' => 'My Profile'])

@php
  $userInitials = collect(explode(' ', $user->name))->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->join('');
@endphp

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="sg-card p-6 text-center h-fit">
    <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center text-white text-2xl font-bold"
      style="background:var(--sg-navy-700)" data-current-user-initials>{{ $userInitials }}</div>
    <h3 class="font-display font-semibold text-lg mt-4" style="color:var(--sg-text)" data-current-user-name>
      {{ $user->name }}
    </h3>
    <p class="text-sm" style="color:var(--sg-text-muted)">System Administrator</p>
    <span class="sg-badge sg-badge-success mt-3 inline-flex">Active</span>
    <div class="mt-5 pt-5 border-t space-y-2 text-left text-sm" style="border-color:var(--sg-border)">
      <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="mail" class="w-4 h-4"></i>
        {{ $user->email }}
      </p>
      <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="phone" class="w-4 h-4"></i>
        {{ $user->phone ?? '0917-555-0100' }}</p>
      <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="building-2" class="w-4 h-4"></i>
        Registrar's Office</p>
    </div>
  </div>

  <div class="lg:col-span-2 sg-card p-6 space-y-4">
    <h3 class="font-display font-semibold" style="color:var(--sg-text)">Account Information</h3>
    <form id="admin-profile-form" method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Full Name</label><input
              type="text" name="name" value="{{ old('name', $user->name) }}" class="sg-input"></div>
        <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Title</label><input
              type="text" value="Dr." class="sg-input"></div>
        <div class="col-span-2"><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email</label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" class="sg-input"></div>
        <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Phone</label><input
              type="text" value="{{ $user->phone ?? '' }}" class="sg-input"></div>
        <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Department</label><input
              type="text" value="Registrar's Office" class="sg-input"></div>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="sg-btn sg-btn-primary" data-toast="Profile updated successfully."><i data-lucide="check"
            class="w-4 h-4"></i> Save Changes</button>
      </div>
    </form>

    <div class="pt-4 mt-4 border-t" style="border-color:var(--sg-border)">
      <h3 class="font-display font-semibold mb-3" style="color:var(--sg-text)">Change Password</h3>
      <form id="admin-password-form" method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2"><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Current Password</label><input type="password" name="current_password" class="sg-input" placeholder="••••••••"></div>
          <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">New Password</label><input type="password" name="password" class="sg-input" placeholder="••••••••"></div>
          <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Confirm Password</label><input type="password" name="password_confirmation" class="sg-input" placeholder="••••••••"></div>
        </div>
        <div class="flex justify-end">
          <button type="submit" class="sg-btn sg-btn-outline" data-toast="Password updated successfully." data-toast-type="success"><i data-lucide="key-round" class="w-4 h-4"></i> Update Password</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
