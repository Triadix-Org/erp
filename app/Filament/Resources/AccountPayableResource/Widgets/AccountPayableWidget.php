<?php

namespace App\Filament\Resources\AccountPayableResource\Widgets;

use App\Models\AccountsPayable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AccountPayableWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalDebtThisMonth = AccountsPayable::whereMonth('date', now()->month)->sum('amount');
        $totalDebtLastMonth = AccountsPayable::whereMonth('date', now()->subMonth()->month)->sum('amount');

        $formattedThisMonth = 'Rp ' . number_format($totalDebtThisMonth, 0, ',', '.');
        $formattedLastMonth = 'Rp ' . number_format($totalDebtLastMonth, 0, ',', '.');

        $percentageChange = $totalDebtLastMonth > 0 ? (($totalDebtThisMonth - $totalDebtLastMonth) / $totalDebtLastMonth) * 100 : 0;
        $formattedPercentageChange = number_format($percentageChange, 2) . '%';
        return [
            Stat::make('Total Hutang Bulan Ini', $formattedThisMonth),
            Stat::make('Total Hutang Bulan Lalu', $formattedLastMonth),
            Stat::make('% Perubahan', $formattedPercentageChange)
        ];
    }
}
