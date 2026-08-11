<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Display the Login View.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle Authentication Request for Students, Faculty, and Admins.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Determine if input is email or student_id
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';

        $credentials = [
            $fieldType => $request->login_id,
            'password' => $request->password,
            'is_active' => true,
        ];

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Dynamic Redirection based on user role
            return match ($user->role) {
                'faculty', 'teacher' => redirect()->intended(route('faculty.dashboard')),
                'student' => redirect()->intended(route('student.dashboard')),
                'admin' => redirect()->intended(route('admin.dashboard')),
                default => redirect()->route('home'),
            };
        }

        throw ValidationException::withMessages([
            'login_id' => ['The credentials provided do not match our records or your account is disabled.'],
        ]);
    }

    /**
     * Terminate user session.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Successfully logged out.');
    }
}