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
        Schema::table('personnel_data', function (Blueprint $table) {
            $table->string('position')->nullable()->change();
            $table->string('dept')->nullable()->change();
            $table->string('div')->nullable()->change();
            $table->string('position_level')->nullable()->change();
            $table->string('employment_status')->nullable()->change();
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
