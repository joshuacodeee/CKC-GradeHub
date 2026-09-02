<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EnrollmentSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /** CKC course catalog used in registration and admin forms. */
    public const COURSES = [
        'BS Elementary Education',
        'BS Secondary Education',
        'BS Business Administration',
        'BS Accountancy',
        'BS Information Technology',
        'BS Social Work',
        'BS Hospitality Management',
        'BS Nursing',
        'BS Criminology',
    ];

    public const YEAR_LEVELS = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];

    public const BLOCKS = ['A', 'B', 'C', 'D', 'E'];

    /** Allowed values for the `sex` column on the user profile. */
    public const SEXES = ['Male', 'Female'];

    public function showRegistrationForm()
    {
        return view('auth.register', [
            'courses' => self::COURSES,
            'yearLevels' => self::YEAR_LEVELS,
            'blocks' => self::BLOCKS,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'sex' => ['required', 'string', 'in:' . implode(',', self::SEXES)],
            'student_id' => ['required', 'string', 'max:255', 'unique:users,student_id'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
                'ends_with:@ckcgingoog.edu.ph',
            ],
            'course' => ['required', 'string', 'in:' . implode(',', self::COURSES)],
            'year_level' => ['required', 'string', 'in:' . implode(',', self::YEAR_LEVELS)],
            'block' => ['nullable', 'string', 'in:' . implode(',', self::BLOCKS)],
            'is_irregular' => ['nullable', 'boolean'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'email.ends_with' => 'Email must be a valid @ckcgingoog.edu.ph address.',
            'course.in' => 'Please choose a course from the list.',
            'year_level.in' => 'Please select a valid year level.',
            'block.in' => 'Please select a valid block (A, B, C, D, or E).',
            'sex.in' => 'Please select a valid sex.',
        ]);

        $user = User::create([
            'firstname' => $validated['firstname'],
            'middlename' => $validated['middlename'] ?? null,
            'lastname' => $validated['lastname'],
            'sex' => $validated['sex'],
            'student_id' => $validated['student_id'],
            'email' => $validated['email'],
            'course' => $validated['course'],
            'year_level' => $validated['year_level'],
            'block' => $validated['block'],
            'is_irregular' => $request->boolean('is_irregular'),
            'password' => $validated['password'],
            'role' => 'student',
            'is_active' => true,
        ]);

        // Set up section-level + class-offering enrollments according to
        // the regular/irregular flag chosen during registration.
        //   • Regular → section row + auto-enrolled curriculum offerings.
        //   • Irregular → section row only; the registrar assigns
        //     individual class offerings via "Manage Enrollment".
        app(EnrollmentSyncService::class)->syncForStudent($user);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard');
    }
}
