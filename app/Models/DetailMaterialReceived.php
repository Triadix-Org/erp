<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailMaterialReceived extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "detail_material_receiveds";
    public $timestamps = true;
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detail) {
            // Tambahkan stok di model Product
            $product = Product::find($detail->product_id);
            if ($product) {
                $product->increment('stock', $detail->qty); // Tambahkan stok
            }
        });

        static::updating(function ($detail) {
            $originalQty = $detail->getOriginal('qty');
            $originalProductId = $detail->getOriginal('product_id');

            // Jika product_id berubah, kurangi stok dari produk lama
            if ($originalProductId !== $detail->product_id) {
                $oldProduct = Product::find($originalProductId);
                if ($oldProduct) {
                    $oldProduct->decrement('stock', $originalQty);
                }

                // Tambahkan stok ke produk baru
                $newProduct = Product::find($detail->product_id);
                if ($newProduct) {
                    $newProduct->increment('stock', $detail->qty);
                }
            } else {
                // Jika hanya qty yang berubah
                $product = Product::find($detail->product_id);
                if ($product) {
                    $qtyDifference = $detail->stock - $originalQty;
                    $product->increment('stock', $qtyDifference);
                }
            }
        });

        static::deleting(function ($detail) {
            // Kurangi stok jika detail dihapus
            $product = Product::find($detail->product_id);
            if ($product) {
                $product->decrement('stock', $detail->qty); // Kurangi stok
            }
        });
    }

    public function header(): BelongsTo
    {
        return $this->belongsTo(HeaderMaterialReceived::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
