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

        $loginId = trim($request->login_id);
        $fieldType = filter_var($loginId, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';

        $credentials = [
            $fieldType => $loginId,
            'password' => $request->password,
            'is_active' => true,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            return match ($user->role) {
                'teacher' => redirect()->intended(route('teacher.dashboard')),
                'student' => redirect()->intended(route('student.dashboard')),
                'admin' => redirect()->intended(route('admin.dashboard')),
                default => redirect()->route('login'),
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

        return redirect()->route('login')->with('info', 'Successfully logged out.');
    }
}