<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // Demo admin
        User::updateOrCreate(
            ['email' => 'admin@ckcgingoog.edu.ph'],
            [
                'name' => 'Ramon Villareal',
                'student_id' => null,
                'course' => null,
                'year_level' => null,
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('admin123'),
            ]
        );

        // Demo teacher (legacy account; the named teachers come from
        // TeacherSeeder)
        User::updateOrCreate(
            ['email' => 'teacher@ckcgingoog.edu.ph'],
            [
                'name' => 'Andrea Reyes',
                'student_id' => null,
                'course' => null,
                'year_level' => null,
                'role' => 'teacher',
                'is_active' => true,
                'password' => Hash::make('teacher123'),
            ]
        );

        // Demo student
        User::updateOrCreate(
            ['email' => 'student@ckcgingoog.edu.ph'],
            [
                'name' => 'Juan Dela Cruz',
                'student_id' => '2026-0001',
                'course' => 'BS Information Technology',
                'year_level' => '1st Year',
                'role' => 'student',
                'is_active' => true,
                'password' => Hash::make('student123'),
            ]
        );
    }
}
