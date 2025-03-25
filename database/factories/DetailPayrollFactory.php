<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailPayroll>
 */
class DetailPayrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payroll_id' => Payroll::first()?->getKey() ?? Payroll::factory()->create()->getKey(),
            'employee_id' => Employee::factory()->create()->getKey(),
            'salary' => $this->faker->numberBetween(3000000, 10000000),
            'total' => function (array $attributes) {
                return $attributes['salary'];
            }
        ];
    }
}
