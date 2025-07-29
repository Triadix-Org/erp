<?php

use App\Models\JobVacancy;
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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(JobVacancy::class);
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->date('date_of_birth');
            $table->string('education')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->string('resume')->nullable();
            $table->string('application_letter')->nullable();
            $table->string('certificate')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
