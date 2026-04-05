<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DayOff>
 */
class DayOffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()?->first()?->getKey() ?? User::factory()->create()->getKey(),
            'lead_id' => User::inRandomOrder()?->first()?->getKey() ?? User::factory()->create()->getKey(),
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(8),
            'total_days' => 2,
            'reason' => $this->faker->sentence(),
        ];
    }
}
