<?php

namespace Database\Factories;

use App\Models\HeaderRequestOrder;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailRequestOrder>
 */
class DetailRequestOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::first()?->getKey() ?? Product::factory()->create()->getKey(),
            'header_request_order_id' => HeaderRequestOrder::first()?->getKey() ?? HeaderRequestOrder::factory()->create()->getKey(),
            'qty' => $this->faker->numberBetween(1, 100),
        ];
    }
}
