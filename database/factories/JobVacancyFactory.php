<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobVacancy>
 */
class JobVacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle(),
            'slug' => function (array $attributes) {
                return Str::slug($attributes['title']);
            },
            'post_date' => now(),
            'close_date' => now()->addWeek(),
            'job_desc' => $this->faker->sentence(30),
            'job_requirements' => $this->faker->sentence(30),
            'salary' => $this->faker->numberBetween(1000000, 10000000),
            'dept_div' => $this->faker->word(),
            'contract_type' => $this->faker->numberBetween(1, 2),
            'working_type' => $this->faker->numberBetween(1, 2),
            'minimum_education' => $this->faker->word(),
            'years_of_experience' => $this->faker->numberBetween(1, 10) . ' Tahun',
            'location' => 'Yogyakarta',
            'status' => 1,
            'published_by' => User::first()?->email ?? User::factory()->create()->email,
        ];
    }
}
