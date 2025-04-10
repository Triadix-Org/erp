<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeaderMaterialReceived extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "header_material_receiveds";
    public $timestamps = true;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $lastest = self::orderBy('code', 'desc')
                ->first();
            $code = 'MRN' . date('Y') . date('m'); // Prefix 'REQ' diikuti tahun saat ini

            if ($lastest) {
                // Ambil nomor urut terakhir setelah kode dan tahun (misalnya 'REQ2024000005')
                $lastNumber = (int) substr($lastest->code, strlen($code));
                // Tambah 1
                $newNumber = $lastNumber + 1;
            } else {
                // Jika belum ada data, mulai dari 1
                $newNumber = 1;
            }

            // Format angka dengan leading zero (pad dengan 6 digit)
            $itemNumber = $code . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
            $model->code = $itemNumber;
        });
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailMaterialReceived::class);
    }

    public function po(): BelongsTo
    {
        return $this->belongsTo(HeaderPurchaseOrder::class, 'header_purchase_order_id', 'id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rec_by(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'received_by');
    }
}
