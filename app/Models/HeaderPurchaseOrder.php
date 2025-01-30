<?php

namespace App\Models;

use App\Enum\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeaderPurchaseOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "header_purchase_orders";
    public $timestamps = true;
    protected $guarded = [];

    protected $casts = [
        'po_date' => 'datetime',
        'payment_due' => 'date',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $lastest = self::orderBy('code', 'desc')
                ->first();
            $code = 'PO' . date('Y') . date('m'); // Prefix 'REQ' diikuti tahun saat ini

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

            $mrn = HeaderRequestOrder::find($model->header_request_order_id);
            $mrn->proses = 1;
            $mrn->save();
        });
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPurchaseOrder::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function header_request_order(): BelongsTo
    {
        return $this->belongsTo(HeaderRequestOrder::class);
    }

    public function purchaser(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'purchaser');
    }

    public function operational(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'operational_by');
    }

    public function finance(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'finance_by');
    }
}
