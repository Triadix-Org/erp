<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "product_categories";
    public $timestamps = true;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $lastest = self::latest('code')->first();
            $code = $lastest ? (int) substr($lastest->code, 2) + 1 : 1;
            $model->code = 'CT' . str_pad($code, 5, '0', STR_PAD_LEFT);
        });
    }
}
