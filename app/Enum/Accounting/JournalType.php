<?php

namespace App\Enum\Accounting;

enum JournalType: string
{
    case GENERAL = 'umum';
    case SALES = 'penjualan';   
    case PURCHASE = 'pembelian';
    case CASH = 'kas-bank';
    case PRODUCTION = 'produksi';
    case PAYROLL = 'payroll';
    case ADJUSTMENT = 'penyesuaian';

    public static function labels(): array
    {
        return [
            self::GENERAL->value => 'Umum',
            self::SALES->value => 'Penjualan',
            self::PURCHASE->value => 'Pembelian',
            self::CASH->value => 'Kas & Bank',
            self::PRODUCTION->value => 'Produksi',
            self::PAYROLL->value => 'Payroll',
            self::ADJUSTMENT->value => 'Penyesuaian',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            JournalType::GENERAL => 'Umum',
            JournalType::SALES => 'Penjualan',
            JournalType::PURCHASE => 'Pembelian',
            JournalType::CASH => 'Kas & Bank',
            JournalType::PRODUCTION => 'Produksi',
            JournalType::PAYROLL => 'Payroll',
            JournalType::ADJUSTMENT => 'Penyesuaian',
        };
    }
}
