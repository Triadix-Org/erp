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
        Schema::create('day_offs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\User::class)->index();
            $table->foreignIdFor(App\Models\User::class, 'lead_id')->index();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('total_days')->default(1);
            $table->text('reason')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->boolean('is_approved_by_lead')->default(false);
            $table->boolean('is_approved_by_hr')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('day_offs');
    }
};
