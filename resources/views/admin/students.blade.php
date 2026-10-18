@extends('layouts.app', ['role' => $role ?? 'admin', 'active' => 'students', 'title' => 'Students'])

@section('content')
    <div class="space-y-5">

        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Student Records</h2>
                <p class="text-sm" style="color:var(--sg-text-muted)">{{ $students->count() }} registered student accounts</p>
            </div>
        </div>

        <div class="sg-card p-5">
            @if (($role ?? 'admin') === 'admin')
                @include('components.table', [
                    'tableId' => 'students-table',
                    'searchPlaceholder' => 'Search by name, ID, or section…',
                    'filterColumn' => 'standing',
                    'filterOptions' => ['Regular', 'Irregular'],
                    'SecondfilterColumn' => 'status',
                    'SecondfilterOptions' => ['Active', 'Inactive'],
                    'addLabel' => 'Add Student',
                    'addModalId' => 'add-student-modal',
                    'addIcon' => 'user-plus',
                ])
            @else
                @include('components.table', [
                    'tableId' => 'students-table',
                    'searchPlaceholder' => 'Search by name, ID, or section…',
                    'filterColumn' => 'standing',
                    'filterOptions' => ['Regular', 'Irregular'],
                    'SecondfilterColumn' => 'status',
                    'SecondfilterOptions' => ['Active', 'Inactive'],
                ])
            @endif

            <div class="overflow-x-auto -mx-5 px-5">
                <table class="sg-table" id="students-table">
                    <thead>
                        <tr>
                            <th class="cursor-pointer" data-sort-column>Student ID</th>
                            <th class="cursor-pointer" data-sort-column>Name</th>
                            <th class="cursor-pointer" data-sort-column>Course</th>
                            <th class="cursor-pointer" data-sort-column>Year Level</th>
                            <th class="cursor-pointer" data-sort-column>Block</th>
                            <th class="cursor-pointer" data-sort-column>Standing</th>
                            <th class="cursor-pointer" data-sort-column>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td style="color:var(--sg-text)">{{ $student->student_id }}</td>
                                <td class="font-medium" style="color:var(--sg-text)">{{ $student->last_name_first }}</td>
                                <td style="color:var(--sg-text-muted)">{{ $student->course ?? '—' }}</td>
                                <td style="color:var(--sg-text-muted)">{{ $student->year_level ?? '—' }}</td>
                                <td style="color:var(--sg-text-muted)">{{ $student->block ?? '—' }}</td>
                                <td data-col="standing">
                                    @if ($student->is_irregular)
                                        <span class="sg-badge sg-badge-warning">Irregular</span>
                                    @else
                                        <span class="sg-badge sg-badge-info">Regular</span>
                                    @endif
                                </td>
                                <td data-col="status"><span
                                        class="sg-badge {{ $student->is_active ? 'sg-badge-success' : 'sg-badge-danger' }}">{{ $student->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="text-right">
                                    @if (($role ?? 'admin') === 'admin')
                                    <a href="{{ route('admin.student-enrollment', $student) }}"
                                        class="sg-btn sg-btn-outline !text-xs !px-2 !py-1">
                                        <i data-lucide="list-checks" class="w-3.5 h-3.5"></i> Manage Enrollment
                                    </a>
                                    @else
                                        <span class="text-xs" style="color:var(--sg-text-muted)">View only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10" style="color:var(--sg-text-muted)">No student
                                    accounts have been
                                    created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div data-empty-state="students-table" class="hidden text-center py-12">
                    <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2" style="color:var(--sg-text-muted)"></i>
                    <p class="text-sm" style="color:var(--sg-text-muted)">No students match your search.</p>
                </div>
            </div>

            <div class="flex items-center justify-between mt-4 pt-4 border-t text-sm" style="border-color:var(--sg-border)">
                <span style="color:var(--sg-text-muted)">Showing {{ $students->count() }} student accounts</span>
                <div class="flex items-center gap-1">
                    <button class="sg-btn sg-btn-outline !px-3 !py-1.5"><i data-lucide="chevron-left"
                            class="w-4 h-4"></i></button>
                    <button class="sg-btn sg-btn-primary !px-3 !py-1.5">1</button>
                    <button class="sg-btn sg-btn-outline !px-3 !py-1.5">2</button>
                    <button class="sg-btn sg-btn-outline !px-3 !py-1.5">3</button>
                    <button class="sg-btn sg-btn-outline !px-3 !py-1.5"><i data-lucide="chevron-right"
                            class="w-4 h-4"></i></button>
                </div>
            </div>
        </div>
    </div>

    @if (($role ?? 'admin') === 'admin')
    {{-- Add Student Modal --}}
    <div id="add-student-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
        <div class="sg-modal sg-surface rounded-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0"
                style="border-color:var(--sg-border)">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:var(--sg-info-bg)"><i
                            data-lucide="user-plus" class="w-4 h-4" style="color:var(--sg-navy-600)"></i></div>
                    <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Add New Student</h3>
                </div>
                <button data-modal-close="add-student-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x"
                        class="w-4 h-4"></i></button>
            </div>
            <form id="add-student-form" method="POST" action="{{ route('admin.students.store') }}"
                class="p-6 overflow-y-auto space-y-4">
                @csrf
                @if ($errors->any())
                    <div class="rounded-lg p-3 text-sm" role="alert" style="background:var(--sg-danger-bg); color:var(--sg-danger)">
                        Please correct the highlighted fields and try again.
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">First Name</label>
                        <input type="text" name="firstname" required placeholder="e.g. Juan" value="{{ old('firstname') }}" class="sg-input">
                        @error('firstname') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Last Name</label>
                        <input type="text" name="lastname" required placeholder="e.g. Dela Cruz" value="{{ old('lastname') }}" class="sg-input">
                        @error('lastname') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Student ID</label>
                        <input type="text" name="student_id" required placeholder="e.g. 2026-0001" value="{{ old('student_id') }}" class="sg-input">
                        @error('student_id') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email</label>
                        <input type="email" name="email" required placeholder="e.g. student@ckcgingoog.edu.ph"
                            value="{{ old('email') }}" class="sg-input">
                        @error('email') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Course</label>
                        <select name="course" class="sg-select" required>
                            @foreach (['BS Elementary Education', 'BS Secondary Education', 'BS Business Administration', 'BS Accountancy', 'BS Information Technology', 'BS Social Work', 'BS Hospitality Management', 'BS Nursing', 'BS Criminology'] as $course)
                                <option value="{{ $course }}" @selected(old('course') === $course)>{{ $course }}</option>
                            @endforeach
                        </select>
                        @error('course') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Year Level</label>
                        <select name="year_level" class="sg-select" required>
                            @foreach (['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'] as $yearLevel)
                                <option value="{{ $yearLevel }}" @selected(old('year_level') === $yearLevel)>{{ $yearLevel }}</option>
                            @endforeach
                        </select>
                        @error('year_level') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Block <span
                                style="color:var(--sg-text-muted)">(optional)</span></label>
                        <select name="block" class="sg-select">
                            <option value="">— None —</option>
                            @foreach (['A', 'B', 'C', 'D', 'E'] as $block)
                                <option value="{{ $block }}" @selected(old('block') === $block)>{{ $block }}</option>
                            @endforeach
                        </select>
                        @error('block') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Password</label>
                    <div class="relative">
                        <input id="student-password" type="password" name="password" required minlength="8" class="sg-input pr-12">
                        <button type="button" data-password-toggle="student-password" class="absolute right-3 top-1/2 -translate-y-1/2" aria-label="Show password">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('password') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Confirm Password</label>
                    <div class="relative">
                        <input id="student-password-confirmation" type="password" name="password_confirmation" required minlength="8" class="sg-input pr-12">
                        <button type="button" data-password-toggle="student-password-confirmation" class="absolute right-3 top-1/2 -translate-y-1/2" aria-label="Show password">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('password_confirmation') <p class="mt-1 text-xs" style="color:var(--sg-danger)">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-2 pt-2">
            <button type="button" data-modal-close="add-student-modal" class="sg-btn sg-btn-outline">Cancel</button>
            <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save
                Student</button>
        </div>
        </form>
    </div>
    </div>
    @endif

    {{-- View Student Modal --}}
    <div id="view-student-modal"
        class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
        <div class="sg-modal sg-surface rounded-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--sg-border)">
                <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Student Details</h3>
                <button data-modal-close="view-student-modal" class="sg-btn sg-btn-ghost !p-2"><i data-lucide="x"
                        class="w-4 h-4"></i></button>
            </div>
            <div class="p-6 space-y-3 text-sm" id="view-student-body"></div>
        </div>
    </div>
    @if ($errors->any() && ($role ?? 'admin') === 'admin')
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('add-student-modal')?.classList.remove('hidden');
                });
            </script>
        @endpush
    @endif
@endsection
