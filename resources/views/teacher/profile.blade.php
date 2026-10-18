@extends('layouts.app', ['role' => 'teacher', 'active' => 'profile', 'title' => 'My Profile'])

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
    <p class="text-sm" style="color:var(--sg-text-muted)">Teacher</p>
    <span class="sg-badge sg-badge-success mt-3 inline-flex">Active</span>
    <div class="mt-5 pt-5 border-t space-y-2 text-left text-sm" style="border-color:var(--sg-border)">
      <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="mail" class="w-4 h-4"></i>
        {{ $user->email }}
      </p>
      <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="phone" class="w-4 h-4"></i>
        {{ $user->phone ?: 'No phone on file' }}</p>
      <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="user-round" class="w-4 h-4"></i>
        Sex: <span style="color:var(--sg-text)">{{ $user->sex ?: '—' }}</span></p>
    </div>
  </div>

  <div class="lg:col-span-2 sg-card p-6 space-y-4">
    <h3 class="font-display font-semibold" style="color:var(--sg-text)">Account Information</h3>
    <form method="POST" action="{{ route('teacher.profile.update') }}" class="space-y-4">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Last Name</label>
          <input type="text" name="lastname" value="{{ old('lastname', $user->lastname) }}" required class="sg-input">
          @error('lastname') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">First Name</label>
          <input type="text" name="firstname" value="{{ old('firstname', $user->firstname) }}" required class="sg-input">
          @error('firstname') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Role</label>
          <input type="text" value="Teacher" class="sg-input" disabled>
        </div>
        <div>
          <span class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Sex</span>
          <div class="flex items-center gap-4 h-[42px]">
            <label class="inline-flex items-center gap-2 text-sm cursor-pointer" style="color:var(--sg-text)">
              <input type="radio" name="sex" value="Male" @checked(old('sex', $user->sex) === 'Male')>
              Male
            </label>
            <label class="inline-flex items-center gap-2 text-sm cursor-pointer" style="color:var(--sg-text)">
              <input type="radio" name="sex" value="Female" @checked(old('sex', $user->sex) === 'Female')>
              Female
            </label>
          </div>
          @error('sex') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email</label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" required
            placeholder="name@ckcgingoog.edu.ph" pattern=".+@ckcgingoog\.edu\.ph" class="sg-input">
          <p class="mt-1 text-xs" style="color:var(--sg-text-muted)">Use your @ckcgingoog.edu.ph account.</p>
          @error('email') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Phone</label>
          <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 0917-555-0100" class="sg-input">
          @error('phone') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
        </div>
      </div>
      <div class="pt-2 flex justify-end">
        <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save Changes</button>
      </div>
    </form>
  </div>
</div>
@endsection
