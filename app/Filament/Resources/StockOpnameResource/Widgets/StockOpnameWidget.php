<?php

namespace App\Filament\Resources\StockOpnameResource\Widgets;

use App\Enum\StockOpnameStatus;
use App\Models\StockOpname;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockOpnameWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $stockOpname = StockOpname::all();
        $open = $stockOpname->where('status', StockOpnameStatus::OPEN->value)->count();
        $approved = $stockOpname->where('status', StockOpnameStatus::APPROVED->value)->count();
        $rev = $stockOpname->where('status', StockOpnameStatus::FIXING->value)->count();
        return [
            Stat::make('Open Stock Opnames', $open),
            Stat::make('Approved Stock Opnames', $approved),
            Stat::make('Need Revision Stock Opnames', $rev)
        ];
    }
}
