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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('payment_first')->nullable()->after('status');
            $table->string('val_payment_first')->nullable()->after('payment_first');
            $table->string('payment_second')->nullable()->after('val_payment_first');
            $table->string('val_payment_second')->nullable()->after('payment_second');
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
