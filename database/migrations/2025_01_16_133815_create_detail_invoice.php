<?php

use App\Models\Invoice;
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
        Schema::create('detail_invoice', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Invoice::class);
            $table->foreignIdFor(Product::class);
            $table->integer('qty')->default(0);
            $table->string('unit')->default();
            $table->string('weight')->nullable();
            $table->string('dimension')->nullable();
            $table->string('price_total')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_invoice');
    }
};
