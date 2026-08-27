<?php

namespace App\Http\Controllers;

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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:255', 'unique:users,student_id'],
            'course' => ['required', 'in:BSIT,BSSW,BSCRIM,BSED,BSA,BSBA,BSN,BSHM'],
            'year_level' => ['required', 'in:1st Year,2nd Year,3rd Year,4th Year'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        User::create([
            ...$validated,
            'role' => 'student',
            'is_active' => true,
        ]);

        return redirect()->route('admin.students')->with('success', 'Student account created successfully.');
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