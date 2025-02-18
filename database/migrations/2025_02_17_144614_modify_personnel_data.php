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
            $table->integer('dept')->change();
            $table->integer('div')->change();
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->integer('status')->default(1)->after('department_head');
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
