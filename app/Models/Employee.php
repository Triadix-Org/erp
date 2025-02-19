<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "employees";
    public $timestamps = true;
    protected $guarded = [];

    public function personnel(): HasOne
    {
        return $this->hasOne(PersonnelData::class, 'nip', 'nip');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $lastest = self::orderBy('nip', 'desc')
                ->first();

            // Ambil tahun (2 digit terakhir) dan bulan dari kolom start_working
            $year = date('y', strtotime($model->start_working)); // 2 digit tahun
            $month = date('m', strtotime($model->start_working)); // Bulan

            $code = $year . $month; // Gabungkan 2 digit tahun dengan bulan dari start_working

            if ($lastest) {
                // Ambil nomor urut terakhir setelah kode dan tahun
                $lastNumber = (int) substr($lastest->nip, strlen($code));
                // Tambah 1
                $newNumber = $lastNumber + 1;
            } else {
                // Jika belum ada data, mulai dari 1
                $newNumber = 1;
            }

            // Format angka dengan leading zero (pad dengan 6 digit)
            $itemNumber = $code . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
            $model->nip = $itemNumber;

            // Insert to personnel data
            $person = new PersonnelData();
            $person->nip = $itemNumber;
            $person->save();
        });
    }
}
