<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPurchaseOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "detail_purchase_orders";
    public $timestamps = true;
    protected $guarded = [];

    public function header(): BelongsTo
    {
        return $this->belongsTo(HeaderPurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
