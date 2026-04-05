<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DayOff extends Model
{
    /** @use HasFactory<\Database\Factories\DayOffFactory> */
    use HasFactory;
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'lead_id',
        'start_date',
        'end_date',
        'total_days',
        'status',
        'reason',
        'is_approved_by_lead',
        'is_approved_by_hr',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => \App\Enum\HumanResource\DayOffStatus::class,
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if ($model->user_id === null) {
                $model->user_id = Auth::user()->getKey();
            }
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
