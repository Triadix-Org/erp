<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    /** @use HasFactory<\Database\Factories\TaxFactory> */
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    protected $guarded = [];

    public function coa(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id', 'id');
    }
}
