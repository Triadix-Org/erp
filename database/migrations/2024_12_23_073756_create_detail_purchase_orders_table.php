<?php

use App\Models\HeaderPurchaseOrder;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(HeaderPurchaseOrder::class);
            $table->foreignIdFor(Product::class);
            $table->integer('qty');
            $table->integer('disc_rp');
            $table->integer('tax_rp');
            $table->integer('total');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_purchase_orders');
    }
};
