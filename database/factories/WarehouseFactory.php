<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'WH' . $this->faker->numberBetween(1, 100),
            'name' => 'Warehouse ' . $this->faker->name(),
            'location' => $this->faker->word(),
            'description' => $this->faker->sentence()
        ];
    }
}
