<?php

namespace Database\Factories;

use App\Models\HeaderPurchaseOrder;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailPurchaseOrder>
 */
class DetailPurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'header_purchase_order_id' => HeaderPurchaseOrder::first()?->getKey() ?? HeaderPurchaseOrder::factory()->create()->getKey(),
            'product_id' => Product::first()?->getKey() ?? Product::factory()->create()->getKey(),
            'qty' => $this->faker->numberBetween(1, 100),
            'total' => function (array $attributes) {
                $product = Product::find($attributes['product_id']);
                $total = $product->price * $attributes['qty'];

                return $total;
            }
        ];
    }
}
