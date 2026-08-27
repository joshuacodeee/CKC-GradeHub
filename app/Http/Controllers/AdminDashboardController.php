<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'studentCount' => User::query()->where('role', 'student')->count(),
            'teacherCount' => User::query()->where('role', 'teacher')->count(),
            'recentStudents' => User::query()->where('role', 'student')->latest()->limit(5)->get(),
        ]);
    }
}