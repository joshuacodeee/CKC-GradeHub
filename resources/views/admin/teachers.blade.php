@extends('layouts.app', ['role' => 'admin', 'active' => 'teachers', 'title' => 'Teachers'])

@section('content')
    <div class="space-y-5">
        <div>
            <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Teacher Records</h2>
            <p class="text-sm" style="color:var(--sg-text-muted)">{{ $teachers->count() }} registered teacher accounts</p>
        </div>

        <div class="sg-card p-5">
            @include('components.table', [
                'tableId' => 'teachers-table',
                'searchPlaceholder' => 'Search by name, ID, or email…',
                'filterColumn' => 'status',
                'filterOptions' => ['Active', 'On Leave'],
                'addLabel' => 'Add Teacher',
                'addModalId' => 'add-teacher-modal',
                'addIcon' => 'user-plus',
            ])

            <div class="overflow-x-auto -mx-5 px-5">
                <table class="sg-table" id="teachers-table">
                    <thead>
                        <tr>
                            <th class="cursor-pointer" data-sort-column>Teacher ID</th>
                            <th class="cursor-pointer" data-sort-column>Name</th>
                            <th class="cursor-pointer" data-sort-column>Email</th>
                            <th class="cursor-pointer" data-sort-column>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teachers as $teacher)
                            <tr>
                                <td style="color:var(--sg-text)">{{ $teacher->id }}</td>
                                <td class="font-medium" style="color:var(--sg-text)">{{ $teacher->name }}</td>
                                <td style="color:var(--sg-text-muted)">{{ $teacher->email }}</td>
                                <td data-col="status">
                                    <span
                                        class="sg-badge {{ $teacher->is_active ? 'sg-badge-success' : 'sg-badge-danger' }}">{{ $teacher->is_active ? 'Active' : 'On Leave' }}</span>
                                </td>
                                <td>
                                    @if ($teacher->is_active)
                                        <form id="deactivate-form-{{ $teacher->id }}" method="POST"
                                            action="{{ route('admin.teacher.deactivate', $teacher->id) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="button"
                                                onclick=" confirmAction( 'Are you sure you want to deactivate this teacher?', {
                                                  title: 'Deactivate Teacher?',
                                                  confirmText: 'Deactivate',
                                                  danger: true }).then((confirmed) => {
                                                        if (confirmed) {
                                                            document.getElementById('deactivate-form-{{ $teacher->id }}').submit();
                                                        }
                                                    })
                                                "
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:border-red-200 hover:bg-red-100">
                                                <i data-lucide="user-x" class="w-3.5 h-3.5"></i>
                                                Deactivate
                                            </button>
                                        </form>
                                    @else
                                        <form id="reactivate-form-{{ $teacher->id }}" method="POST"
                                            action="{{ route('admin.teacher.reactivate', $teacher->id) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="button"
                                                onclick=" confirmAction( 'Are you sure you want to reactivate this teacher?', {
                                                  title: 'Reactivate Teacher?',
                                                  confirmText: 'Reactivate',
                                                  danger: false }).then((confirmed) => {
                                                        if (confirmed) {
                                                            document.getElementById('reactivate-form-{{ $teacher->id }}').submit();
                                                        }
                                                    })
                                                "
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-green-100 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-600 transition hover:border-green-200 hover:bg-green-100">
                                                <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                                                Reactivate
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-10" style="color:var(--sg-text-muted)">No teacher
                                    accounts have been
                                    created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div data-empty-state="teachers-table" class="hidden text-center py-12">
                    <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
                    <p class="text-sm" style="color:var(--sg-text-muted)">No teachers match your search.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Teacher Modal --}}
    <div id="add-teacher-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
        <div class="sg-modal sg-surface rounded-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0"
                style="border-color:var(--sg-border)">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:var(--sg-info-bg)"><i
                            data-lucide="user-plus" class="w-4 h-4" style="color:var(--sg-navy-600)"></i></div>
                    <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Add New Teacher</h3>
                </div>
                <button data-modal-close="add-teacher-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x"
                        class="w-4 h-4"></i></button>
            </div>
            <form id="add-teacher-form" method="POST" action="{{ route('admin.teachers.store') }}"
                class="p-6 overflow-y-auto space-y-4">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">First Name</label>
                        <input type="text" name="firstname" required placeholder="e.g. Andrea" class="sg-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">
                            Middle Name <span class="text-xs" style="color:var(--sg-text-muted)">(optional)</span>
                        </label>
                        <input type="text" name="middlename" placeholder="e.g. Cruz" class="sg-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Last Name</label>
                        <input type="text" name="lastname" required placeholder="e.g. Reyes" class="sg-input">
                    </div>
                    <div>
                        <span class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Sex</span>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-2 text-sm cursor-pointer"
                                style="color:var(--sg-text)">
                                <input type="radio" name="sex" value="Male" required>
                                Male
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm cursor-pointer"
                                style="color:var(--sg-text)">
                                <input type="radio" name="sex" value="Female">
                                Female
                            </label>
                        </div>
                        @error('sex')
                            <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email Address</label>
                        <input type="email" name="email" required placeholder="name@nbit.edu.ph" class="sg-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Password</label>
                        <input type="password" name="password" required minlength="8" class="sg-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Confirm
                            Password</label>
                        <input type="password" name="password_confirmation" required minlength="8" class="sg-input">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-4 border-t" style="border-color:var(--sg-border)">
                    <button type="button" data-modal-close="add-teacher-modal"
                        class="sg-btn sg-btn-outline">Cancel</button>
                    <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check"
                            class="w-4 h-4 mr-2"></i>Save Teacher</button>
                </div>
            </form>
        </div>
    </div>
@endsection
