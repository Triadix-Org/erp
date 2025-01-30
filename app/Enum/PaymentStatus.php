<?php

namespace App\Enum;

enum PaymentStatus: int
{
    case UNPAID = 0;
    case PAID = 1;
    case OVER = 2;

    public static function labels(): array
    {
        return [
            self::UNPAID->value => 'Unpaid',
            self::PAID->value => 'Paid',
            self::OVER->value => 'Overdue',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            PaymentStatus::UNPAID => 'Unpaid',
            PaymentStatus::PAID => 'Paid',
            PaymentStatus::OVER => 'Overdue',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'warning',
            self::PAID => 'success',
            self::OVER => 'danger',
        };
    }
}
