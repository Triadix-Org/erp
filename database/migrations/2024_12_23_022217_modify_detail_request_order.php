<?php

use App\Models\HeaderRequestOrder;
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
        Schema::table('detail_request_orders', function (Blueprint $table) {
            $table->foreignIdFor(HeaderRequestOrder::class)->after('id');
            $table->foreignIdFor(Product::class)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_request_orders', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('item_code');
        });
    }
};
