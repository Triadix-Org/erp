<?php

namespace Database\Factories;

use App\Models\HeaderMaterialReceived;
use App\Models\HeaderPurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HeaderMaterialReceived>
 */
class HeaderMaterialReceivedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lastest = HeaderMaterialReceived::orderBy('code', 'desc')
            ->first();
        $code = 'MRN' . date('Y') . date('m');

        if ($lastest) {
            $lastNumber = (int) substr($lastest->code, strlen($code));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format angka dengan leading zero (pad dengan 6 digit)
        $itemNumber = $code . str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        return [
            'code' => $itemNumber,
            'header_purchase_order_id' => HeaderPurchaseOrder::first()?->getKey() ?? HeaderPurchaseOrder::factory()->create()->getKey(),
            'date' => now(),
            'delivery_date' => now(),
            'supplier_id' => Supplier::first()?->getKey() ?? Supplier::factory()->create()->getKey(),
            'received_by' => User::first()?->email ?? 'root@mail.com',
            'total_items' => $this->faker->numberBetween(1, 100),
            'total_amount' => $this->faker->numberBetween(100000, 10000000),
            'received_condition' => $this->faker->sentence(10),
            'comment' => $this->faker->sentence(10),
            'qc_by' => User::first()?->email ?? 'root@mail.com',
        ];
    }
}
