<?php

namespace Database\Factories;

use App\Models\HeaderPurchaseOrder;
use App\Models\HeaderRequestOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HeaderPurchaseOrder>
 */
class HeaderPurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lastest = HeaderPurchaseOrder::orderBy('code', 'desc')
            ->first();
        $code = 'PO' . date('Y') . date('m');

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
            'po_date' => now(),
            'purchaser' => User::first()?->email ?? 'root@mail.com',
            'supplier_id' => Supplier::first()?->getKey() ?? Supplier::factory()->create()->getKey(),
            'header_request_order_id' => HeaderRequestOrder::first()?->getKey() ?? HeaderRequestOrder::factory()->create()->getKey(),
            'payment_terms' => 'Pay after delivered',
            'pamyent_due' => now()->addMonth(),
            'payment_status' => 0,
            'incoterms' => $this->faker->sentence(),
        ];
    }
}
