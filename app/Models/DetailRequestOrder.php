<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailRequestOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "detail_request_orders";
    public $timestamps = true;
    protected $guarded = [];

    public function header(): BelongsTo
    {
        return $this->belongsTo(HeaderRequestOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
