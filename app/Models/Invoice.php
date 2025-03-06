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
                $lastNumber = (int) substr($lastest->inv_no, strlen($code));
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

        static::created(function ($model) {
            $accountRcv                 = new AccountsReceivable();
            $accountRcv->invoice_id     = $model->getKey();
            $accountRcv->customer_id    = $model->customer_id;
            $accountRcv->date           = now();
            $accountRcv->due_date       = $model->payment_due;
            $accountRcv->amount         = $model->total_amount;
            $accountRcv->save();
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function headerSalesOrder(): BelongsTo
    {
        return $this->belongsTo(HeaderSalesOrder::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailInvoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
