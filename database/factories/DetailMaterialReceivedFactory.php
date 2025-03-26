<?php

namespace Database\Factories;

use App\Models\HeaderMaterialReceived;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailMaterialReceived>
 */
class DetailMaterialReceivedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'header_material_received_id' => HeaderMaterialReceived::first()?->getKey() ?? HeaderMaterialReceived::factory()->create()->getKey(),
            'product_id' => Product::first()?->getKey() ?? Product::factory()->create()->getKey(),
            'qty' => $this->faker->numberBetween(1, 100),
        ];
    }
}
