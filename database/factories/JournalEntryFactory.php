<?php

namespace Database\Factories;

use App\Enum\Accounting\JournalSource;
use App\Models\HeaderPurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ref' => HeaderPurchaseOrder::first()?->code ?? HeaderPurchaseOrder::factory()->create()->code,
            'date' => now(),
            'description' => $this->faker->sentence(4),
            'source' => $this->faker->randomElement(JournalSource::cases()),
            'source_id' => HeaderPurchaseOrder::first()?->getKey() ?? HeaderPurchaseOrder::factory()->create()->code,
        ];
    }
}
