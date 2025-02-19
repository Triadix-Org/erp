<?php

namespace App\Filament\Resources\IncomeResource\Widgets;

use App\Models\Income;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IncomeWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAmount = Income::sum('amount');
        $formattedAmount = 'Rp ' . number_format($totalAmount, 0, ',', '.');

        return [
            Stat::make('Income This Month', $formattedAmount)
                ->icon('heroicon-s-chart-bar')
        ];
    }
}
