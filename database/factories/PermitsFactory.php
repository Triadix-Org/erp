<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permits>
 */
class PermitsFactory extends Factory
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
            'type' => $this->faker->randomElement(\App\Enum\HumanResource\PermitType::cases())->value,
            'date' => $this->faker->date,
            'reason' => $this->faker->paragraph,
            'attachment' => $this->faker->filePath,
        ];
    }
}
