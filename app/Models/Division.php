<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    /** @use HasFactory<\Database\Factories\DivisionFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    public $timestamps = true;

    public function directora(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director', 'id');
    }
}
