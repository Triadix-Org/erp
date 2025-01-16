<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = true;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $lastest = self::orderBy('inv_no', 'desc')
                ->first();
            $code = 'INV' . date('Y') . date('m');

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
            $model->inv_no          = $itemNumber;
            $model->user_id         = Auth::user()->id;
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sales_order(): BelongsTo
    {
        return $this->belongsTo(HeaderSalesOrder::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailInvoice::class);
    }
}
