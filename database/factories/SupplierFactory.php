<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'V' . $this->faker->numberBetween(1, 100),
            'name' => $this->faker->word(),
            'pic' => $this->faker->name(),
            'handphone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'address' => $this->faker->address(),
            'desc' => $this->faker->sentence(),
            'status' => 1,
            'payment_first' => 'BNI',
            'val_payment_first' => $this->faker->numberBetween(1000, 1000000),
            'payment_second' => 'BCA',
            'val_payment_second' => $this->faker->numberBetween(1000, 1000000)
        ];
    }
}
