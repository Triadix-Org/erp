<?php

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
        Schema::table('header_purchase_orders', function (Blueprint $table) {
            $table->date('payment_due')->after('payment_terms')->nullable();
            $table->integer('payment_status')->after('payment_due')->nullable();
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('payment_status')->after('payment_due')->nullable();
            $table->integer('status')->after('shipment_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
