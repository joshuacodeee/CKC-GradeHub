@extends('layouts.app', ['role' => 'admin', 'active' => 'registrars', 'title' => 'Registrars'])

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Registrar Accounts</h2>
                <p class="text-sm" style="color:var(--sg-text-muted)">{{ $registrars->count() }} registrar accounts</p>
            </div>
            <button type="button" data-modal-open="add-registrar-modal" class="sg-btn sg-btn-primary">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Add Registrar
            </button>
        </div>

        <div class="sg-card p-5 overflow-x-auto">
            <table class="sg-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registrars as $registrar)
                        <tr>
                            <td class="font-medium" style="color:var(--sg-text)">{{ $registrar->name }}</td>
                            <td style="color:var(--sg-text-muted)">{{ $registrar->email }}</td>
                            <td>
                                <span class="sg-badge {{ $registrar->is_active ? 'sg-badge-success' : 'sg-badge-danger' }}">
                                    {{ $registrar->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-10" style="color:var(--sg-text-muted)">
                                No registrar accounts have been created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="add-registrar-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
        <div class="sg-modal sg-surface rounded-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--sg-border)">
                <h3 class="font-display font-semibold" style="color:var(--sg-text)">Add Registrar</h3>
                <button type="button" data-modal-close="add-registrar-modal" class="sg-btn sg-btn-ghost !p-2" aria-label="Close">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="add-registrar-form" method="POST" action="{{ route('admin.registrars.store') }}" class="p-6 overflow-y-auto space-y-4">
                @csrf
                @if ($errors->any())
                    <div class="rounded-lg p-3 text-sm" role="alert" style="background:var(--sg-danger-bg); color:var(--sg-danger)">
                        Please correct the highlighted fields and try again.
                    </div>
                @endif
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="registrar-firstname" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">First Name</label>
                        <input id="registrar-firstname" type="text" name="firstname" value="{{ old('firstname') }}" required class="sg-input">
                        @error('firstname') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="registrar-middlename" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Middle Name <span class="text-xs" style="color:var(--sg-text-muted)">(optional)</span></label>
                        <input id="registrar-middlename" type="text" name="middlename" value="{{ old('middlename') }}" class="sg-input">
                    </div>
                    <div>
                        <label for="registrar-lastname" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Last Name</label>
                        <input id="registrar-lastname" type="text" name="lastname" value="{{ old('lastname') }}" required class="sg-input">
                        @error('lastname') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="registrar-email" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email</label>
                        <input id="registrar-email" type="email" name="email" value="{{ old('email') }}" required class="sg-input">
                        @error('email') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="registrar-password" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Password</label>
                        <div class="relative">
                            <input id="registrar-password" type="password" name="password" required minlength="8" class="sg-input pr-12">
                            <button type="button" data-password-toggle="registrar-password" class="absolute right-3 top-1/2 -translate-y-1/2" aria-label="Show password">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                        @error('password') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="registrar-password-confirmation" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Confirm Password</label>
                        <div class="relative">
                            <input id="registrar-password-confirmation" type="password" name="password_confirmation" required minlength="8" class="sg-input pr-12">
                            <button type="button" data-password-toggle="registrar-password-confirmation" class="absolute right-3 top-1/2 -translate-y-1/2" aria-label="Show password">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-modal-close="add-registrar-modal" class="sg-btn sg-btn-outline">Cancel</button>
                    <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save Registrar</button>
                </div>
            </form>
        </div>
    </div>
    @if ($errors->any())
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('add-registrar-modal')?.classList.remove('hidden');
                });
            </script>
        @endpush
    @endif
@endsection
