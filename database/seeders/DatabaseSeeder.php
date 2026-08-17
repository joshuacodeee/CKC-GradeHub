<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // Admin
    User::firstOrCreate(
        ['email' => 'admin@ckcgingoog.edu.ph'],
        [
            'name' => 'System Administrator',
            'student_id' => 'ADMIN-001',
            'role' => 'admin',
            'is_active' => true,
            'password' => 'admin123',
        ]
    );

    // Student
    User::firstOrCreate(
        ['email' => 'student@ckcgingoog.edu.ph'],
        [
            'name' => 'Juan Dela Cruz',
            'student_id' => 'STUDENT-001',
            'role' => 'student',
            'is_active' => true,
            'password' => 'student123',
        ]
    );

    // Teacher
    User::firstOrCreate(
        ['email' => 'teacher@ckcgingoog.edu.ph'],
        [
            'name' => 'Maria Santos',
            'student_id' => 'TEACHER-001',
            'role' => 'teacher',
            'is_active' => true,
            'password' => 'teacher123',
        ]
    );
}
}
