<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYear = SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::latest()->first();

        if (!$schoolYear) {
            $this->command->warn('No school year found. Run SchoolYearSeeder first.');
            return;
        }

        $defaultAdviser = User::where('role', 'teacher')->first();

        // Spec §46 calls out: BSIT 1-A, BSIT 1-B, BSED 1-A, BSBA 1-A.
        $sections = [
            ['name' => 'BSIT 1-A',  'course' => 'BS Information Technology', 'year_level' => '1st Year', 'block' => 'A'],
            ['name' => 'BSIT 1-B',  'course' => 'BS Information Technology', 'year_level' => '1st Year', 'block' => 'B'],
            ['name' => 'BSIT 2-A',  'course' => 'BS Information Technology', 'year_level' => '2nd Year', 'block' => 'A'],
            ['name' => 'BSED 1-A',  'course' => 'BS Elementary Education',  'year_level' => '1st Year', 'block' => 'A'],
            ['name' => 'BSBA 1-A',  'course' => 'BS Business Administration','year_level' => '1st Year', 'block' => 'A'],
        ];

        foreach ($sections as $sec) {
            $section = Section::firstOrCreate(
                [
                    'name' => $sec['name'],
                    'school_year_id' => $schoolYear->id,
                ],
                [
                    'course' => $sec['course'],
                    'year_level' => $sec['year_level'],
                    'adviser_id' => $defaultAdviser?->id,
                    'is_active' => true,
                ]
            );

            // Bring legacy rows up to date with the current course naming
            // and (optional) block label.
            if (
                $section->course !== $sec['course']
                || $section->year_level !== $sec['year_level']
                || ($sec['block'] ?? null) !== $section->block
            ) {
                $section->update([
                    'course' => $sec['course'],
                    'year_level' => $sec['year_level'],
                    'block' => $sec['block'] ?? $section->block,
                ]);
            }
        }
    }
}
