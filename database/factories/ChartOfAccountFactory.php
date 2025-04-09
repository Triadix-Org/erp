<?php

namespace Database\Factories;

use App\Enum\Accounting\CoaType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChartOfAccount>
 */
class ChartOfAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->numberBetween(1000, 9999),
            'name' => $this->faker->name(),
            'type' => $this->faker->randomElement(CoaType::cases()),
            'description' => $this->faker->sentence(9)
        ];
    }
}
