<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Payroll;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class);
            $table->foreignIdFor(Payroll::class);
            $table->integer('salary')->default(0);
            $table->integer('overtime')->default(0);
            $table->integer('bonus')->default(0);
            $table->integer('cut')->default(0);
            $table->integer('total')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_payrolls');
    }
};
