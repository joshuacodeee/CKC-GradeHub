<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminProfileController extends Controller
{
    public function profile()
    {
        return view('admin.profile', [
            'user' => auth()->user(),
            'role' => auth()->user()->role,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $sexes = \App\Http\Controllers\Auth\RegisterController::SEXES;

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'sex' => [
                auth()->user()->role === 'registrar' ? 'nullable' : 'required',
                'string',
                'in:' . implode(',', $sexes),
            ],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:32'],
        ], [
            'sex.in' => 'Please select a valid sex.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
