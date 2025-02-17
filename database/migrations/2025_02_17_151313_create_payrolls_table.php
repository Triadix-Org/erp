<?php

use App\Models\Employee;
use App\Models\PersonnelData;
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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PersonnelData::class);
            $table->foreignIdFor(Employee::class);
            $table->integer('salary')->default(0);
            $table->integer('overtime')->default(0);
            $table->integer('bonus')->default(0);
            $table->integer('cut')->default(0);
            $table->integer('total')->default(0);
            $table->string('month')->nullable();
            $table->integer('year')->nullable();
            $table->integer('status')->default(0);
            $table->date('paid_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
