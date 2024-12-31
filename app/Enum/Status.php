<?php

namespace App\Enum;

enum Status: int
{
    case NONACTIVE = 0;
    case ACTIVE = 1;

    public static function labels(): array
    {
        return [
            self::ACTIVE->value => 'Active',
            self::NONACTIVE->value => 'Non Active',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            Status::ACTIVE => 'Active',
            Status::NONACTIVE => 'Non Active',
        };
    }
}
