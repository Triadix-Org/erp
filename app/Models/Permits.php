<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Permits extends Model
{
    /** @use HasFactory<\Database\Factories\PermitsFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'lead_id',
        'type',
        'date',
        'reason',
        'attachment',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'type' => \App\Enum\HumanResource\PermitType::class,
        'status' => \App\Enum\ApprovalStatus::class
    ];
    
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->user_id = Auth::user()->getKey();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_id');
    }
}
