<?php

namespace App\Enum\Accounting;

enum TaxType: string
{
    case SALES = 'Penjualan';
    case PURCHASE = 'Pembelian';

    public static function labels(): array
    {
        return [
            self::SALES->value => 'Penjualan',
            self::PURCHASE->value => 'Pembelian',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            TaxType::SALES => 'Penjualan',
            TaxType::PURCHASE => 'Pembelian',
        };
    }
}
