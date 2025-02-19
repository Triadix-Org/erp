<?php

namespace App\Models;

use App\Enum\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Throwable;

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
            $code = 'PO' . date('Y') . date('m');

            if ($lastest) {
                $lastNumber = (int) substr($lastest->code, strlen($code));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            // Format angka dengan leading zero (pad dengan 6 digit)
            $itemNumber = $code . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
            $model->code = $itemNumber;
        });

        static::created(function ($model) {
            DB::transaction(function () use ($model) {
                // Update proses di HeaderRequestOrder
                $mrn = HeaderRequestOrder::find($model->header_request_order_id);
                if ($mrn) {
                    $mrn->proses = 1;
                    $mrn->save();
                }

                // Buat record di AccountsPayable
                $accountPayable = new AccountsPayable();
                $accountPayable->header_purchase_order_id   = $model->id;
                $accountPayable->supplier_id                = $model->supplier_id;
                $accountPayable->date                       = now();
                $accountPayable->due_date                   = $model->payment_due;
                $accountPayable->amount                     = $model->total_amount;
                $accountPayable->save();
            });
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
