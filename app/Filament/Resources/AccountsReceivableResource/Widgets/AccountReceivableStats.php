<?php

namespace App\Filament\Resources\AccountReceivableResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AccountReceivableStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Unpaid', function () {
                    $unpaid = \App\Models\AccountsReceivable::where('status', 0)->sum('amount');
                    return format_currency($unpaid);
                })
                ->description('Total amount of unpaid accounts receivable')
                ->icon('heroicon-o-x-circle')
                ->color('warning'),
            Stat::make('Paid', function () {
                    $paid = \App\Models\AccountsReceivable::where('status', 1)->sum('amount');
                    return format_currency($paid);
                })
                ->description('Total amount of paid accounts receivable')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Overdue', function () {
                    $overdue = \App\Models\AccountsReceivable::where('status', 2)->sum('amount');
                    return format_currency($overdue);
                })
                ->description('Total amount of overdue accounts receivable')
                ->icon('heroicon-o-exclamation-circle')
                ->color('danger'),
        ];
    }
}
