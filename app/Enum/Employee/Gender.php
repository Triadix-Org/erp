<?php

namespace App\Enum\Employee;

enum Gender: int
{
    case LK = 0;
    case PR = 1;

    public static function labels(): array
    {
        return [
            self::LK->value => 'Laki-Laki',
            self::PR->value => 'Perempuan',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            Gender::LK => 'Laki-Laki',
            Gender::PR => 'Perempuan',
        };
    }
}
