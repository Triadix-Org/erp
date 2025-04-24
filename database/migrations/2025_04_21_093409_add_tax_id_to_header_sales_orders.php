<?php

use App\Models\Tax;
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
        Schema::table('header_sales_orders', function (Blueprint $table) {
            $table->foreignIdFor(Tax::class)->nullable()->after('app_manager_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('header_sales_orders', function (Blueprint $table) {
            //
        });
    }
};
