<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HeaderMaterialReceived extends Model
{
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
}
