<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsPayable extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    protected $guarded = [];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(HeaderPurchaseOrder::class, 'header_purchase_order_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
