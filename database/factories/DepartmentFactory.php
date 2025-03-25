<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'division_id' => Division::first()?->getKey() ?? Division::factory()->create()->getKey(),
            'department_head' => User::first()?->getKey() ?? User::factory()->create()->getKey(),
        ];
    }
}
