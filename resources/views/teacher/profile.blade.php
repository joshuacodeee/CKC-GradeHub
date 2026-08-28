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
    <p class="text-sm" style="color:var(--sg-text-muted)">
      {{ $user->department ?? 'College of Computer Studies' }}
    </p>
    <span class="sg-badge sg-badge-success mt-3 inline-flex">Active</span>
    <div class="mt-5 pt-5 border-t space-y-2 text-left text-sm" style="border-color:var(--sg-border)">
      <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="mail" class="w-4 h-4"></i>
        {{ $user->email }}
      </p>
      <p class="flex items-center gap-2" style="color:var(--sg-text-muted)"><i data-lucide="book-open" class="w-4 h-4"></i>
        {{ $subjects ? $subjects->join(', ') : '—' }}
      </p>
      <div class="flex items-center gap-2" style="color:var(--sg-text-muted)">
        <i data-lucide="layers" class="w-4 h-4"></i>
        <span>
          @if($assignments && $assignments->count() > 0)
            @foreach($assignments as $assignment)
              {{ $assignment->section->name ?? '—' }}
              @if(!$loop->last), @endif
            @endforeach
          @else
            —
          @endif
        </span>
      </div>
    </div>
  </div>

  <div class="lg:col-span-2 sg-card p-6 space-y-4">
    <h3 class="font-display font-semibold" style="color:var(--sg-text)">Account Information</h3>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Full Name</label><input type="text"
            value="{{ $user->name }}" class="sg-input"></div>
      <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Employee ID</label><input
            type="text" value="T-{{ $user->id }}" class="sg-input" disabled></div>
      <div class="col-span-2"><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email</label>
        <input type="email" value="{{ $user->email }}" class="sg-input"></div>
      <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Department</label><input
            type="text" value="{{ $user->department ?? '—' }}" class="sg-input"></div>
    </div>
    <div class="pt-2 flex justify-end">
      <button class="sg-btn sg-btn-primary" data-toast="Profile updated successfully."><i data-lucide="check"
          class="w-4 h-4"></i> Save Changes</button>
    </div>
  </div>
</div>
@endsection
