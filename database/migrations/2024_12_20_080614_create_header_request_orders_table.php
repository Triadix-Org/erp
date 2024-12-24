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
        Schema::create('header_request_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('req_by');
            $table->date('date');
            $table->date('due_date');
            $table->string('note')->nullable();
            $table->integer('app_manager')->default(0);
            $table->string('approved_by')->nullable();
            $table->integer('status')->default(0);
            $table->integer('proses')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_request_orders');
    }
};
