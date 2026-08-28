<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeders\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends WithoutModelEvents
{
    public function run(): void
    {
        // Demo admin
        User::create([
            'name' => 'Ramon Villareal',
            'email' => 'admin@ckcgingoog.edu.ph',
            'student_id' => null,
            'course' => null,
            'year_level' => null,
            'role' => 'admin',
            'is_active' => true,
            'password' => Hash::make('admin123'),
        ]);

        // Demo teacher
        $teacher = User::create([
            'name' => 'Andrea Reyes',
            'email' => 'teacher@ckcgingoog.edu.ph',
            'student_id' => null,
            'course' => null,
            'year_level' => null,
            'role' => 'teacher',
            'is_active' => true,
            'password' => Hash::make('teacher123'),
        ]);

        // Demo student
        User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'student@ckcgingoog.edu.ph',
            'student_id' => '2026-0001',
            'course' => 'BSIT',
            'year_level' => '1st Year',
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('student123'),
        ]);
    }
}
