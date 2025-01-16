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
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->date('post_date');
            $table->date('close_date')->nullable();
            $table->string('job_desc')->nullable();
            $table->string('job_requirements')->nullable();
            $table->integer('salary')->nullable();
            $table->string('dept_div')->nullable();
            $table->integer('contract_type')->nullable();
            $table->integer('working_type')->nullable();
            $table->string('minimum_education')->nullable();
            $table->string('years_of_experience')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};
