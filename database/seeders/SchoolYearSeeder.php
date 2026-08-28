<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use Illuminate\Database\Console\Seeders\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Previous school years (archived)
        SchoolYear::create([
            'name' => '2024–2025',
            'semester' => 'Second Semester',
            'is_current' => false,
            'is_active' => false,
        ]);

        SchoolYear::create([
            'name' => '2025–2026',
            'semester' => 'First Semester',
            'is_current' => false,
            'is_active' => false,
        ]);

        // Current school year
        SchoolYear::create([
            'name' => '2026–2027',
            'semester' => 'First Semester',
            'is_current' => true,
            'is_active' => true,
        ]);
    }
}
