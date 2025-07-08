<?php

namespace App\Models;

use App\Enum\Accounting\JournalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    /** @use HasFactory<\Database\Factories\JournalEntryFactory> */
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    protected $guarded = [];

    protected $casts = [
        'type' => JournalType::class
    ];

    public function details(): HasMany
    {
        return $this->hasMany(DetailJournalEntry::class);
    }

    public function periods(): BelongsTo
    {
        return $this->belongsTo(AccountingPeriods::class, 'accounting_periods_id');
    }
}
