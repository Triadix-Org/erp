<?php

namespace App\Enum\Employee;

enum Religion: int
{
    case ISLAM = 0;
    case KRISTEN = 1;
    case KATOLIK = 2;
    case HINDU = 3;
    case BUDHA = 4;
    case KONGHUCHU = 5;
    case LAINNYA = 6;

    public static function labels(): array
    {
        return [
            self::ISLAM->value => 'Islam',
            self::KRISTEN->value => 'Kristen',
            self::KATOLIK->value => 'Katolik',
            self::HINDU->value => 'Hindu',
            self::BUDHA->value => 'Budha',
            self::KONGHUCHU->value => 'Kong Hu Chu',
            self::LAINNYA->value => 'Lainnya',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            Religion::ISLAM => 'Islam',
            Religion::KRISTEN => 'Kristen',
            Religion::KATOLIK => 'Katolik',
            Religion::HINDU => 'Hindu',
            Religion::BUDHA => 'Budha',
            Religion::KONGHUCHU => 'Kong Hu Chu',
            Religion::LAINNYA => 'Lainnya',
        };
    }
}
