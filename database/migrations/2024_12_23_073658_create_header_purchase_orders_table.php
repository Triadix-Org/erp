<?php

use App\Models\HeaderRequestOrder;
use App\Models\Supplier;
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
        Schema::create('header_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->timestamp('po_date');
            $table->string('purchaser');
            $table->foreignIdFor(Supplier::class);
            $table->foreignIdFor(HeaderRequestOrder::class)->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('incoterms')->nullable();
            $table->integer('app_operational')->default(0);
            $table->string('operational_by')->nullable();
            $table->integer('app_finance')->default(0);
            $table->string('finance_by')->nullable();
            $table->integer('vendor_confirm')->default(0);
            $table->integer('status')->default(1);
            $table->integer('subtotal')->default(0);
            $table->integer('total_tax')->default(0);
            $table->integer('total_disc')->default(0);
            $table->integer('total_amount')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_purchase_orders');
    }
};
