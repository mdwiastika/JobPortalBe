<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPosting>
 */
class JobPostingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recruiter_id' => $this->faker->numberBetween(31, 40),
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraphs(3, true),
            'requirements' => $this->faker->paragraphs(2, true),
            'employment_type' => $this->faker->randomElement(['full_time', 'part_time', 'contract', 'internship']),
            'experience_level' => $this->faker->randomElement(['beginner', 'medium', 'expert']),
            'work_type' => $this->faker->randomElement(['on_site', 'remote', 'hybrid']),
            'min_salary' => $this->faker->numberBetween(1000000, 2000000),
            'max_salary' => $this->faker->numberBetween(6000000, 8000000),
            'location' => $this->faker->city,
            'is_disability' => false,

        ];
    }
}
