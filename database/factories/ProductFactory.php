<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'IT' . $this->faker->numberBetween(1, 1000),
            'warehouse_id' => Warehouse::first()?->getKey() ?? Warehouse::factory()->create()->getKey(),
            'name' => $this->faker->name(),
            'desc' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(1000, 100000),
            'category_code' => ProductCategory::first()?->code ?? ProductCategory::factory()->create()->code,
            'unit' => 'Pcs',
            'status' => 1,
            'stock' => $this->faker->numberBetween(1, 1000),
            'weight' => $this->faker->numberBetween(100, 10000),
            'dimension' => '100x100x90'
        ];
    }
}
