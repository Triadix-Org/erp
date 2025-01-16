<?php

use App\Models\Customer;
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
        Schema::create('header_sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->timestamp('sales_date');
            $table->foreignIdFor(Customer::class);
            $table->integer('total_amount')->default(0);
            $table->string('note')->nullable();
            $table->string('payment_terms')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_sales_orders');
    }
};
