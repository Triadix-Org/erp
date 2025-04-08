<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountingPeriods extends Model
{
    /** @use HasFactory<\Database\Factories\AccountingPeriodsFactory> */
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    protected $guarded = [];
}
