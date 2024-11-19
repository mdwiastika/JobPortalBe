<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobPostingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobCategories = JobCategory::all();
        $skills = Skill::all();

        JobPosting::factory(30)
            ->create()
            ->each(function ($jobPosting) use ($jobCategories, $skills) {
                $randomCategories = $jobCategories->random(2);
                $randomSkills = $skills->random(3);
                $jobPosting->jobCategories()->attach($randomCategories->pluck('id'));
                $jobPosting->skills()->attach($randomSkills->pluck('id'));
            });
    }
}
