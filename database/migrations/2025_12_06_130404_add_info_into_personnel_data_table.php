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
            $table->date('start_working')->nullable()->after('bank_account_name');
            $table->date('end_working')->nullable()->after('start_working');
            $table->integer('leave_quota')->default(0)->after('end_working');
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
