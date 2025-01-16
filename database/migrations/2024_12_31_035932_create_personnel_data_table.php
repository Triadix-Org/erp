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
        Schema::create('personnel_data', function (Blueprint $table) {
            $table->id();
            $table->string('nip');
            $table->string('position');
            $table->string('dept');
            $table->string('div');
            $table->string('position_level');
            $table->string('employment_status');
            $table->string('npwp')->nullable();
            $table->string('bank')->nullable();
            $table->integer('bank_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_data');
    }
};
