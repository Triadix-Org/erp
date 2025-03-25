<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payroll>
 */
class PayrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'month' => Carbon::createFromFormat('m', $this->faker->numberBetween(1, 12))->format('F'),
            'year'  => $this->faker->numberBetween(2000, 2030),
            'total_amount' => $this->faker->numberBetween(1, 10000000),
        ];
    }
}
