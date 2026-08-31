<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SchoolYearSeeder::class,
            SubjectSeeder::class,
            SectionSeeder::class,
            TeacherSeeder::class,
            DemoUserSeeder::class,
            TeacherAssignmentSeeder::class,
            EnrollmentSeeder::class,
        ]);
    }
}
