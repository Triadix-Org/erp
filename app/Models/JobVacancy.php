<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JobVacancy extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "job_vacancies";
    protected $appends = ['contract_type_str', 'working_type_str'];
    public $timestamps = true;
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (JobVacancy $jobVacancy) {
            $jobVacancy->slug = Str::slug($jobVacancy->title);
        });
    }

    public function applicants(): BelongsToMany
    {
        return $this->belongsToMany(JobApplication::class);
    }

    public function getContractTypeStrAttribute()
    {
        switch ($this->contract_type) {
            case "1":
                return 'Internship';
                break;

            case "2":
                return 'Full time';
                break;

            case "3":
                return 'Part time';
                break;

            case "4":
                return 'Freelance';
                break;

            case "5":
                return 'Contract';
                break;
            default:
                return '';
                break;
        }
    }

    public function getWorkingTypeStrAttribute()
    {
        switch ($this->working_type) {
            case "1":
                return 'WFO';
                break;

            case "2":
                return 'Remote';
                break;

            default:
                return '';
                break;
        }
    }
}
