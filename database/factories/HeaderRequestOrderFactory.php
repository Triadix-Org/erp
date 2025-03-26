<?php

namespace Database\Factories;

use App\Models\HeaderRequestOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HeaderRequestOrder>
 */
class HeaderRequestOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lastest = HeaderRequestOrder::orderBy('code', 'desc')
            ->first();
        $code = 'REQ' . date('Y') . date('m'); // Prefix 'REQ' diikuti tahun saat ini

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

        return [
            'code' => $itemNumber,
            'req_by' => User::first()?->email ?? 'admin@gmail.com',
            'date' => now(),
            'due_date' => now()->addWeek(),
            'note' => $this->faker->sentence(10),
            'status' => 1
        ];
    }
}
