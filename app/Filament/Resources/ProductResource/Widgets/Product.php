<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product as ModelsProduct;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Product extends BaseWidget
{
    protected function getStats(): array
    {
        $product = ModelsProduct::isActive()->get();
        $totalProduct = $product->count();
        $minimStock = $product->where('stock', '<', 100)->count();
        $dangerStock = $product->where('stock', '<', 10)->count();

        return [
            Stat::make('Total Active Product', $totalProduct)
                ->icon('heroicon-m-chart-bar'),
            Stat::make('Stock Warning (under 100 Pcs)', $minimStock)
                ->icon('heroicon-m-exclamation-triangle'),
            Stat::make('Stock Danger (under 10 Pcs)', $dangerStock)
                ->icon('heroicon-m-exclamation-circle'),
        ];
    }
}
