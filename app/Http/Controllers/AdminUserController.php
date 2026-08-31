<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function students()
    {
        return view('admin.students', [
            'students' => User::query()->where('role', 'student')->latest()->get(),
        ]);
    }

    public function storeStudent(Request $request)
    {
        $courses = \App\Http\Controllers\Auth\RegisterController::COURSES;
        $yearLevels = \App\Http\Controllers\Auth\RegisterController::YEAR_LEVELS;
        $blocks = \App\Http\Controllers\Auth\RegisterController::BLOCKS;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:255', 'unique:users,student_id'],
            'course' => ['required', 'in:' . implode(',', $courses)],
            'year_level' => ['required', 'in:' . implode(',', $yearLevels)],
            'block' => ['nullable', 'in:' . implode(',', $blocks)],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
                'ends_with:@ckcgingoog.edu.ph',
            ],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'email.ends_with' => 'Email must be a valid @ckcgingoog.edu.ph address.',
            'course.in' => 'Please choose a course from the list.',
            'year_level.in' => 'Please select a valid year level.',
            'block.in' => 'Please select a valid block (A, B, C, D, or E).',
        ]);

        $student = User::create([
            ...$validated,
            'role' => 'student',
            'is_active' => true,
        ]);

        // Auto-enroll the new student into a matching section so the assigned
        // teacher can see them. We look up the current school year and the
        // section whose course/year_level/block all match the student's
        // profile. If a section isn't found we skip silently — the admin can
        // adjust the section or the student's course later.
        $this->autoEnrollStudent($student);

        return redirect()->route('admin.students')->with('success', 'Student account created successfully.');
    }

    /**
     * Find a section matching the student's course / year_level / block in
     * the current school year and create an enrollment row for them. Block
     * matching is exact: a student with block "B" only matches a section
     * with block "B".
     */
    protected function autoEnrollStudent(User $student): void
    {
        $schoolYear = SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::where('is_active', true)->latest('id')->first();

        $studentBlock = $student->block ?: null;

        $section = Section::query()
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('course', $student->course)
            ->where('year_level', $student->year_level)
            ->when($studentBlock === null, function ($q) {
                $q->where(function ($q) {
                    $q->whereNull('block')->orWhere('block', '');
                });
            })
            ->when($studentBlock !== null, fn ($q) => $q->where('block', $studentBlock))
            ->where('is_active', true)
            ->first();

        if (! $section) {
            return;
        }

        // Don't create a duplicate enrollment for the same section + year.
        $alreadyEnrolled = Enrollment::where('student_id', $student->id)
            ->where('section_id', $section->id)
            ->when($schoolYear, fn ($q) => $q->where('school_year_id', $schoolYear->id))
            ->exists();

        if ($alreadyEnrolled) {
            return;
        }

        Enrollment::create([
            'student_id' => $student->id,
            'section_id' => $section->id,
            'school_year_id' => $schoolYear?->id,
            'status' => 'enrolled',
        ]);
    }

    public function teachers()
    {
        return view('admin.teachers', [
            'teachers' => User::query()->where('role', 'teacher')->latest()->get(),
        ]);
    }

    public function storeTeacher(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        User::create([
            ...$validated,
            'role' => 'teacher',
            'is_active' => true,
        ]);

        return redirect()->route('admin.teachers')->with('success', 'Teacher account created successfully.');
    }
}