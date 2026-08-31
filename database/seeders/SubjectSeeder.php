<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['code' => 'PE 1',   'name' => 'Physical Education 1', 'description' => 'Foundational fitness, wellness, and movement competencies for college students.', 'units' => 2, 'department' => 'Physical Education'],
            ['code' => 'IT 101', 'name' => 'Introduction to Information Technology', 'description' => 'Survey of computing fundamentals, hardware, software, and the role of IT in modern organizations.', 'units' => 3, 'department' => 'College of Computer Studies'],
            ['code' => 'MATH 101', 'name' => 'Mathematics in the Modern World', 'description' => 'Quantitative reasoning, patterns, and real-world problem solving.', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'GE 101', 'name' => 'Communication Skills I', 'description' => 'Effective written and oral communication in academic and professional contexts.', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'GE 102', 'name' => 'Communication Skills II', 'description' => 'Advanced academic writing and research communication.', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'GE 103', 'name' => 'The Filipino Experience', 'description' => 'Philippine history, identity, and contemporary social realities.', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'GE 104', 'name' => 'Philosophy of the Human Person', 'description' => 'Philosophical inquiry into the human person, freedom, and meaning.', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'GE 105', 'name' => 'Science, Technology, and Society', 'description' => 'Interaction of science, technology, and society across history.', 'units' => 3, 'department' => 'General Education'],
            ['code' => 'NSTP 101', 'name' => 'National Service Training Program', 'description' => 'Civic consciousness and community engagement for first-year students.', 'units' => 3, 'department' => 'Human Resource Development'],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['code' => $subject['code']],
                $subject + ['is_active' => true]
            );
        }
    }
}
