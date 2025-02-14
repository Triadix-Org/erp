<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $randomLetters = strtoupper(Str::random(4));

            // Set kode model menjadi 4 huruf acak kapital
            $model->code = $randomLetters;
        });
    }
}
