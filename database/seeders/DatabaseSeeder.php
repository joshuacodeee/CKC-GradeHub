<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeders\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SchoolYearSeeder::class,
            SubjectSeeder::class,
            SectionSeeder::class,
            DemoUserSeeder::class,
        ]);
    }
}
