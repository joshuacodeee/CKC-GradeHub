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
            ['firstname' => 'Prof. Juan',  'lastname' => 'Dela Cruz', 'email' => 'juan.delacruz@ckcgingoog.edu.ph'],
            ['firstname' => 'Prof. Maria', 'lastname' => 'Santos',    'email' => 'maria.santos@ckcgingoog.edu.ph'],
            ['firstname' => 'Prof. Pedro', 'lastname' => 'Reyes',     'email' => 'pedro.reyes@ckcgingoog.edu.ph'],
        ];

        foreach ($teachers as $row) {
            User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'firstname' => $row['firstname'],
                    'lastname' => $row['lastname'],
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
