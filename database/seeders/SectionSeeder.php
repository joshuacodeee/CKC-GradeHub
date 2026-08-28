<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Database\Console\Seeders\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends WithoutModelEvents
{
    public function run(): void
    {
        $schoolYear = SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::latest()->first();

        if (!$schoolYear) {
            $this->command->warn('No school year found. Run SchoolYearSeeder first.');
            return;
        }

        // Get or create a teacher to use as adviser
        $defaultAdviser = User::where('role', 'teacher')->first();

        $sections = [
            ['name' => 'BSIT 1-A', 'course' => 'BSIT', 'year_level' => '1st Year'],
            ['name' => 'BSIT 1-B', 'course' => 'BSIT', 'year_level' => '1st Year'],
            ['name' => 'BSIT 2-A', 'course' => 'BSIT', 'year_level' => '2nd Year'],
            ['name' => 'BSCS 1-A', 'course' => 'BSCRIM', 'year_level' => '1st Year'],
            ['name' => 'BSED 1-A', 'course' => 'BSED', 'year_level' => '1st Year'],
        ];

        foreach ($sections as $sec) {
            Section::create([
                'name' => $sec['name'],
                'course' => $sec['course'],
                'year_level' => $sec['year_level'],
                'adviser_id' => $defaultAdviser?->id,
                'school_year_id' => $schoolYear->id,
                'is_active' => true,
            ]);
        }
    }
}
