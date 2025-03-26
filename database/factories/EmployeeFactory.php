<?php

namespace Database\Factories;

use App\Enum\Employee\Gender;
use App\Enum\Employee\MarriageStatus;
use App\Enum\Employee\Religion;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        // Ambil tahun dan bulan sekarang
        $yearMonth = Carbon::now()->format('ym');

        // Ambil nomor terakhir dari database untuk bulan ini
        $lastOrder = Employee::where('nip', 'like', "$yearMonth%")
            ->orderBy('order_number', 'desc')
            ->first();

        // Hitung nomor urut berikutnya
        $nextNumber = $lastOrder ? ((int)substr($lastOrder->order_number, -6)) + 1 : 1;

        $nip = $yearMonth . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return [
            'nip' => $nip,
            'name' => $this->faker->name(),
            'nik' => $this->faker->numberBetween(1000000, 100000000),
            'place_of_birth' => $this->faker->city(),
            'date_of_birth' => $this->faker->date(),
            'gender' => $this->faker->randomNumber(Gender::cases()),
            'religion' => $this->faker->randomNumber(Religion::cases()),
            'marriage_status' => $this->faker->randomNumber(MarriageStatus::cases()),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'emergency_phone' => $this->faker->phoneNumber(),
            'start_working' => now()
        ];
    }
}
