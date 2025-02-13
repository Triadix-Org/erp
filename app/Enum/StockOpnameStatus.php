<?php

namespace App\Enum;

enum StockOpnameStatus: int
{
    case OPEN = 0;
    case PENDING = 1;
    case FIXING = 2;
    case APPROVED = 3;

    public static function labels(): array
    {
        return [
            self::OPEN->value => 'Open',
            self::PENDING->value => 'Pending',
            self::FIXING->value => 'Need Revision',
            self::APPROVED->value => 'Approved',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            StockOpnameStatus::OPEN => 'Open',
            StockOpnameStatus::PENDING => 'Pending',
            StockOpnameStatus::FIXING => 'Need Revision',
            StockOpnameStatus::APPROVED => 'Approved',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'info',
            self::PENDING => 'warning',
            self::FIXING => 'danger',
            self::APPROVED => 'success',
        };
    }
}
