<?php

namespace Database\Factories;

use App\Enum\Accounting\TaxType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tax>
 */
class TaxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'type' => $this->faker->randomElement(TaxType::cases()),
            'rate' => $this->faker->randomFloat(2, 0, 20),
        ];
    }
}
