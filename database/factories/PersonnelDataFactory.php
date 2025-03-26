<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PersonnelData>
 */
class PersonnelDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nip' => Employee::first()?->nip ?? Employee::factory()->create()->nip,
            'position' => $this->faker->jobTitle(),
            'dept' => Department::first()?->getKey() ?? Department::factory()->create()->getKey(),
            'div' => Division::first()?->getKey() ?? Division::factory()->create()->getKey(),
            'position_level' => $this->faker->sentence(2),
            'employment_status' => $this->faker->numberBetween(1, 3),
            'npwp' => $this->faker->randomNumber(4, true),
            'bank' => $this->faker->word(),
            'bank_number' => $this->faker->randomNumber(10, true),
            'bank_account_name' => $this->faker->name(),
            'sallary' => $this->faker->randomNumber(8, true)
        ];
    }
}
