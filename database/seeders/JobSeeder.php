<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\JobVacancy;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $job1 = Job::create([
            'title' => 'Software Engineer',
            'description' => 'Develop and maintain software applications.',
            'department' => 'IT',
        ]);

        JobVacancy::create([
            'job_id' => $job1->id,
            'position' => 'Junior Software Engineer',
            'salary' => 50000,
            'deadline' => now()->addDays(30),
        ]);

        JobVacancy::create([
            'job_id' => $job1->id,
            'position' => 'Senior Software Engineer',
            'salary' => 80000,
            'deadline' => now()->addDays(45),
        ]);

        $job2 = Job::create([
            'title' => 'Data Analyst',
            'description' => 'Analyze data and provide insights.',
            'department' => 'Analytics',
        ]);

        JobVacancy::create([
            'job_id' => $job2->id,
            'position' => 'Data Analyst',
            'salary' => 60000,
            'deadline' => now()->addDays(20),
        ]);
    }
}
