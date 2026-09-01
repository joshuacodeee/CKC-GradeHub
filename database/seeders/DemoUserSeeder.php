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
                'firstname' => 'Ramon',
                'lastname' => 'Villareal',
                'student_id' => null,
                'course' => null,
                'year_level' => null,
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
