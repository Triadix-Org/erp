<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DetailPayroll;

class Payroll extends Model
{
    /** @use HasFactory<\Database\Factories\PayrollFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    public $timestamps = true;

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPayroll::class);
    }
}
