<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        // Spec §46 demo teachers. These accounts are the ones the admin
        // will assign to subjects; password is shared for demo simplicity.
        $teachers = [
            ['name' => 'Prof. Juan Dela Cruz', 'email' => 'juan.delacruz@ckcgingoog.edu.ph'],
            ['name' => 'Prof. Maria Santos',   'email' => 'maria.santos@ckcgingoog.edu.ph'],
            ['name' => 'Prof. Pedro Reyes',    'email' => 'pedro.reyes@ckcgingoog.edu.ph'],
        ];

        foreach ($teachers as $row) {
            User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'student_id' => null,
                    'course' => null,
                    'year_level' => null,
                    'role' => 'teacher',
                    'is_active' => true,
                    'password' => Hash::make('teacher123'),
                ]
            );
        }
    }
}
