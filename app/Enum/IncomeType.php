<?php

namespace App\Enum;

enum IncomeType: int
{
    case SALES = 1;
    case OTHER = 2;

    public static function labels(): array
    {
        return [
            self::SALES->value => 'Sales',
            self::OTHER->value => 'Other Income',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            IncomeType::SALES => 'Sales',
            IncomeType::OTHER => 'Other Income',
        };
    }
}
