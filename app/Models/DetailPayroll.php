<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPayroll extends Model
{
    /** @use HasFactory<\Database\Factories\DetailPayrollFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    public $timestamps = true;
}
