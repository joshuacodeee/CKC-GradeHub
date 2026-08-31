<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'name' => ['required', 'string', 'max:255'],
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
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'email.ends_with' => 'Email must be a valid @ckcgingoog.edu.ph address.',
            'course.in' => 'Please choose a course from the list.',
            'year_level.in' => 'Please select a valid year level.',
            'block.in' => 'Please select a valid block (A, B, C, D, or E).',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'student_id' => $validated['student_id'],
            'email' => $validated['email'],
            'course' => $validated['course'],
            'year_level' => $validated['year_level'],
            'block' => $validated['block'],
            'password' => $validated['password'],
            'role' => 'student',
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard');
    }
}
