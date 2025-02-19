<?php

namespace App\Enum;

enum OutcomeType: int
{
    case MATERIAL = 1;
    case PACKING = 2;
    case HUMANRESOURCE = 3;
    case OPERATIONAL = 4;
    case OTHER = 5;

    public static function labels(): array
    {
        return [
            self::MATERIAL->value => 'Raw Material',
            self::PACKING->value => 'Packing',
            self::HUMANRESOURCE->value => 'Human Resource',
            self::OPERATIONAL->value => 'Company Operational',
            self::OTHER->value => 'Other Outcome',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            OutcomeType::MATERIAL => 'Raw Material',
            OutcomeType::PACKING => 'Packing',
            OutcomeType::HUMANRESOURCE => 'Human Resource',
            OutcomeType::OPERATIONAL => 'Company Operational',
            OutcomeType::OTHER => 'Other Outcome',
        };
    }
}
