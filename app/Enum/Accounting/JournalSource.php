<?php

namespace App\Enum\Accounting;

enum JournalSource: string
{
    case PO = 'purchase-order';
    case SALES = 'sales-order';
    case PAYROLL = 'payroll';

    public static function labels(): array
    {
        return [
            self::PO->value => 'Purchase Order',
            self::SALES->value => 'Sales Order',
            self::PAYROLL->value => 'Payroll',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            JournalSource::PO => 'Purchase Order',
            JournalSource::SALES => 'Sales Order',
            JournalSource::PAYROLL => 'Payroll',
        };
    }
}
