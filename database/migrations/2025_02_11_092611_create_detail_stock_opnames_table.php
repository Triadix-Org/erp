<?php

use App\Models\Product;
use App\Models\StockOpname;
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
        Schema::create('detail_stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(StockOpname::class);
            $table->foreignIdFor(Product::class);
            $table->integer('stock_system')->default(0);
            $table->integer('stock_actual')->default(0);
            $table->integer('gap')->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_stock_opnames');
    }
};
