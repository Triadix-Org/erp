<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "job_applications";
    public $timestamps = true;
    protected $guarded = [];

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class);
    }
}
