<?php

namespace App\Enum\Employee;

enum Education: string
{
    case SMA = 'sma/k';
    case DIPLOMA = 'd1-d3';
    case BACHELOR = 'd4/s1';
    case MASTER = 's2';

    public static function labels(): array
    {
        return [
            self::SMA->value => 'SMA/K',
            self::DIPLOMA->value => 'D1 - D3',
            self::BACHELOR->value => 'D4/S1',
            self::MASTER->value => 'S2',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            Education::SMA => 'SMA/K',
            Education::DIPLOMA => 'D1 - D3',
            Education::BACHELOR => 'D4/S1',
            Education::MASTER => 'S2',
        };
    }
}
