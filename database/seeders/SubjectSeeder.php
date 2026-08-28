<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeders\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $subjects = [
            ['code' => 'IT101', 'name' => 'Introduction to Programming', 'units' => 3, 'department' => 'College of Computer Studies'],
            ['code' => 'IT102', 'name' => 'Computer Programming 1', 'units' => 3, 'department' => 'College of Computer Studies'],
            ['code' => 'IT103', 'name' => 'Computer Programming 2', 'units' => 3, 'department' => 'College of Computer Studies'],
            ['code' => 'GE101', 'name' => 'Communication Skills I', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'GE102', 'name' => 'Communication Skills II', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'GE103', 'name' => 'Mathematics in the Modern World', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'GE104', 'name' => 'Science, Technology, and Society', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'GE105', 'name' => 'The Filipino Experience', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'GE106', 'name' => 'Philosophy of the Human Person', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'GE107', 'name' => 'Survey of Economics', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'PE101', 'name' => 'Physical Fitness & wellness', 'units' => 2, 'department' => 'Physical Education'],
            ['code' => 'NSTP101', 'name' => 'National Service Training Program', 'units' => 3, 'department' => 'Human Resource Development'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
