<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    public $timestamps = true;

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'department_head');
    }
}
