<?php

namespace App\Enum\HumanResource;

enum PermitType: string
{
    case LATE = 'late';
    case LEAVE = 'leave';
    case SICK = 'sick';
    case OTHER = 'other';

    public static function labels(): array
    {
        return [
            self::LATE->value => 'Terlambat',
            self::LEAVE->value => 'Tidak Masuk',
            self::SICK->value => 'Sakit',
            self::OTHER->value => 'Lainnya',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::LATE => 'Terlambat',
            self::LEAVE => 'Tidak Masuk',
            self::SICK => 'Sakit',
            self::OTHER => 'Lainnya',
        };
    }
}
