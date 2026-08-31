<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYear = SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::latest()->first();
        if (!$schoolYear) {
            $this->command->warn('No school year; run SchoolYearSeeder first.');
            return;
        }

        $bsit = Section::where('name', 'BSIT 1-A')->where('school_year_id', $schoolYear->id)->first();
        $bsed = Section::where('name', 'BSED 1-A')->where('school_year_id', $schoolYear->id)->first();
        $bsba = Section::where('name', 'BSBA 1-A')->where('school_year_id', $schoolYear->id)->first();

        // The demo student from DemoUserSeeder is already on the system —
        // just enroll them.
        $juan = User::where('email', 'student@ckcgingoog.edu.ph')->first();
        if ($juan && $bsit) {
            Enrollment::firstOrCreate(
                [
                    'student_id' => $juan->id,
                    'section_id' => $bsit->id,
                    'school_year_id' => $schoolYear->id,
                ],
                ['status' => 'enrolled']
            );
        }

        // A few extra students per section so the teacher grade sheet has
        // something to show.
        $extraStudents = [
            // (name, email, course, year_level, section)
            ['Ana Lim',           'ana.lim@ckcgingoog.edu.ph',           'BS Information Technology', '1st Year', $bsit],
            ['Carlo Mendoza',     'carlo.mendoza@ckcgingoog.edu.ph',     'BS Information Technology', '1st Year', $bsit],
            ['Diana Cruz',        'diana.cruz@ckcgingoog.edu.ph',        'BS Information Technology', '1st Year', $bsit],
            ['Eli Navarro',       'eli.navarro@ckcgingoog.edu.ph',       'BS Information Technology', '1st Year', $bsit],

            ['Rita Aquino',       'rita.aquino@ckcgingoog.edu.ph',       'BS Elementary Education',  '1st Year', $bsed],
            ['Tito Belmonte',     'tito.belmonte@ckcgingoog.edu.ph',     'BS Elementary Education',  '1st Year', $bsed],

            ['Lara Domingo',      'lara.domingo@ckcgingoog.edu.ph',      'BS Business Administration','1st Year', $bsba],
            ['Mark Esperanza',    'mark.esperanza@ckcgingoog.edu.ph',    'BS Business Administration','1st Year', $bsba],
        ];

        foreach ($extraStudents as [$name, $email, $course, $yearLevel, $section]) {
            if (!$section) continue;
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'student_id' => '2026-' . str_pad((string) random_int(100, 999), 4, '0', STR_PAD_LEFT),
                    'course' => $course,
                    'year_level' => $yearLevel,
                    'role' => 'student',
                    'is_active' => true,
                    'password' => Hash::make('student123'),
                ]
            );

            Enrollment::firstOrCreate(
                [
                    'student_id' => $user->id,
                    'section_id' => $section->id,
                    'school_year_id' => $schoolYear->id,
                ],
                ['status' => 'enrolled']
            );
        }
    }
}
