<?php

use App\Models\HeaderPurchaseOrder;
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
        Schema::create('header_material_receiveds', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignIdFor(HeaderPurchaseOrder::class);
            $table->date('date');
            $table->date('delivery_date')->nullable();
            $table->foreignIdFor(Supplier::class);
            $table->string('received_by')->nullable();
            $table->integer('total_items')->default(0);
            $table->integer('total_amount')->default(0);
            $table->string('received_condition')->nullable();
            $table->string('comment')->nullable();
            $table->string('qc_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_material_receiveds');
    }
};
