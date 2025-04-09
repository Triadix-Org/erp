<?php

namespace App\Enum\Accounting;

enum CoaType: string
{
    case ASSET = 'Aset';
    case LIABILITY = 'Liabilitas';
    case EQUITY = 'Ekuitas';
    case REVENUE = 'Pendapatan';
    case EXPENSE = 'Beban';

    public static function labels(): array
    {
        return [
            self::ASSET->value => 'Aset',
            self::LIABILITY->value => 'Liabilitas',
            self::EQUITY->value => 'Ekuitas',
            self::REVENUE->value => 'Pendapatan',
            self::EXPENSE->value => 'Beban',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            CoaType::ASSET => 'Aset',
            CoaType::LIABILITY => 'Liabilitas',
            CoaType::EQUITY => 'Ekuitas',
            CoaType::REVENUE => 'Pendapatan',
            CoaType::EXPENSE => 'Beban',
        };
    }
}
