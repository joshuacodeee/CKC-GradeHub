<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\User;
use App\Services\EnrollmentSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function students()
    {
        return view('admin.students', [
            'students' => User::query()
                ->where('role', 'student')
                ->alphabetical()
                ->get(),
        ]);
    }

    public function storeStudent(Request $request)
    {
        $courses = \App\Http\Controllers\Auth\RegisterController::COURSES;
        $yearLevels = \App\Http\Controllers\Auth\RegisterController::YEAR_LEVELS;
        $blocks = \App\Http\Controllers\Auth\RegisterController::BLOCKS;

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:255', 'unique:users,student_id'],
            'course' => ['required', 'in:' . implode(',', $courses)],
            'year_level' => ['required', 'in:' . implode(',', $yearLevels)],
            'block' => ['nullable', 'in:' . implode(',', $blocks)],
            'is_irregular' => ['nullable', 'boolean'],
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
            'is_irregular' => $request->boolean('is_irregular', false),
            'role' => 'student',
            'is_active' => true,
        ]);

        // Auto-enroll the new student into a matching section so the assigned
        // teacher can see them. We look up the current school year and the
        // section whose course/year_level/block all match the student's
        // profile. If a section isn't found we skip silently — the admin can
        // adjust the section or the student's course later. For regular
        // students this also adds per-class-offering memberships.
        $this->autoEnrollStudent($student);

        return redirect()->route('admin.students')->with('success', 'Student account created successfully.');
    }

    /**
     * Find a section matching the student's course / year_level / block in
     * the current school year and create an enrollment row for them.
     * Delegates to EnrollmentSyncService so the per-class-offering
     * memberships are kept in step with the student's profile.
     *
     * Flow:
     *   • Regular students → section row + auto-enrolled curriculum offerings.
     *   • Irregular students → section row only; the registrar assigns
     *     individual class offerings via "Manage Enrollment".
     */
    protected function autoEnrollStudent(User $student): void
    {
        $service = app(EnrollmentSyncService::class);
        $schoolYear = $service->currentSchoolYear();
        $service->syncForStudent($student, $schoolYear, Auth::user());
    }

    public function teachers()
    {
        return view('admin.teachers', [
            'teachers' => User::query()
                ->where('role', 'teacher')
                ->alphabetical()
                ->get(),
        ]);
    }

    public function storeTeacher(Request $request)
    {
        $sexes = \App\Http\Controllers\Auth\RegisterController::SEXES;

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'sex' => ['required', 'string', 'in:' . implode(',', $sexes)],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'sex.in' => 'Please select a valid sex.',
        ]);

        User::create([
            ...$validated,
            'role' => 'teacher',
            'is_active' => true,
        ]);

        return redirect()->route('admin.teachers')->with('success', 'Teacher account created successfully.');
    }
}