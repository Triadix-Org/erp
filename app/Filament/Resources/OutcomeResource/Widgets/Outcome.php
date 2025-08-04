<?php

namespace App\Filament\Resources\OutcomeResource\Widgets;

use App\Models\Outcome as ModelsOutcome;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Outcome extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAmount = ModelsOutcome::sum('amount');
        $formattedAmount = 'Rp ' . number_format($totalAmount, 0, ',', '.');

        return [
            Stat::make('Total', $formattedAmount)
                ->icon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }
}
