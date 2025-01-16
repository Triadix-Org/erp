<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeaderSalesOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = ['status_str'];
    protected $table = "header_sales_orders";
    public $timestamps = true;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $lastest = self::orderBy('code', 'desc')
                ->first();
            $code = 'ORD' . date('Y') . date('m'); // Prefix 'REQ' diikuti tahun saat ini

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
        return $this->hasMany(DetailSalesOrder::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sales_by(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'sales_by');
    }

    public function approved_by(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'app_manager_by');
    }

    public function getStatusStrAttribute()
    {
        switch ($this->status) {
            case "0":
                return 'Open';
                break;

            case "1":
                return 'On Process';
                break;

            case "2":
                return 'Declined';
                break;

            case "3":
                return 'Finished';
                break;
            default:
                return '';
                break;
        }
    }
}
