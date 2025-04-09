<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountingPeriods extends Model
{
    /** @use HasFactory<\Database\Factories\AccountingPeriodsFactory> */
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    protected $guarded = [];

    public function scopeOpen(Builder $query): void
    {
        $query->where('is_closed', 0);
    }
}
