<?php

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
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
        Schema::create('detail_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(JournalEntry::class);
            $table->foreignIdFor(ChartOfAccount::class)->nullable()->constrained()->cascadeOnUpdate();
            $table->string('description')->nullable();
            $table->integer('debit')->default(0);
            $table->integer('kredit')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_journal_entries');
    }
};
