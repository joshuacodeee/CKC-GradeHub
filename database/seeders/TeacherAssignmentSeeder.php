<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYear = SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::latest()->first();
        if (!$schoolYear) {
            $this->command->warn('No school year; run SchoolYearSeeder first.');
            return;
        }

        $teacher = fn (string $email) => User::where('email', $email)->first();
        $subject = fn (string $code) => Subject::where('code', $code)->first();
        $section = fn (string $name) => Section::where('name', $name)
            ->where('school_year_id', $schoolYear->id)
            ->first();

        $semester = 'First Semester';

        // Spec §42: PE 1 is taught by three different teachers to three
        // different sections. Each teacher should only see their own
        // section.
        $assignments = [
            ['teacher' => 'juan.delacruz@ckcgingoog.edu.ph', 'subject' => 'PE 1',  'section' => 'BSIT 1-A'],
            ['teacher' => 'maria.santos@ckcgingoog.edu.ph',   'subject' => 'PE 1',  'section' => 'BSED 1-A'],
            ['teacher' => 'pedro.reyes@ckcgingoog.edu.ph',    'subject' => 'PE 1',  'section' => 'BSBA 1-A'],

            // Juan also handles IT 101 to BSIT 1-A so he has a second class.
            ['teacher' => 'juan.delacruz@ckcgingoog.edu.ph', 'subject' => 'IT 101','section' => 'BSIT 1-A'],

            // IT 101 also taught to BSIT 1-B (different teacher, same subject).
            ['teacher' => 'maria.santos@ckcgingoog.edu.ph',   'subject' => 'IT 101','section' => 'BSIT 1-B'],
        ];

        foreach ($assignments as $row) {
            $t = $teacher($row['teacher']);
            $s = $subject($row['subject']);
            $sec = $section($row['section']);

            if (!$t || !$s || !$sec) {
                $this->command->warn("Skipping missing row: teacher={$row['teacher']} subject={$row['subject']} section={$row['section']}");
                continue;
            }

            TeacherSubject::firstOrCreate(
                [
                    'teacher_id' => $t->id,
                    'subject_id' => $s->id,
                    'section_id' => $sec->id,
                    'school_year_id' => $schoolYear->id,
                    'semester' => $semester,
                ],
                [
                    'status' => 'active',
                ]
            );
        }
    }
}
