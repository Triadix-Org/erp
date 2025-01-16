<?php

namespace App\Enum\Employee;

enum MarriageStatus: int
{
    case KAWIN = 1;
    case BELUMKAWIN = 0;

    public static function labels(): array
    {
        return [
            self::KAWIN->value => 'Kawin',
            self::BELUMKAWIN->value => 'Belum/Tidak Kawin',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            MarriageStatus::KAWIN => 'Kawin',
            MarriageStatus::BELUMKAWIN => 'Belum/Tidak Kawin',
        };
    }
}
