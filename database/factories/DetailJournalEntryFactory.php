<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailJournalEntry>
 */
class DetailJournalEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entries_id' => JournalEntry::first()?->getKey() ?? JournalEntry::factory()->create()->getKey(),
            'chart_of_account_id' => ChartOfAccount::first()?->getKey() ?? ChartOfAccount::factory()->create()->getKey(),
            'description' => $this->faker->sentence(4),
            'debit' => $this->faker->numberBetween(1000, 9999999),
            'kredit' => $this->faker->numberBetween(1000, 9999999),
        ];
    }
}
