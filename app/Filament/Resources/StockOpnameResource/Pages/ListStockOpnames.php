<?php

namespace App\Filament\Resources\StockOpnameResource\Pages;

use App\Filament\Resources\StockOpnameResource;
use App\Filament\Resources\StockOpnameResource\Widgets\StockOpnameWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockOpnames extends ListRecords
{
    protected static string $resource = StockOpnameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Stock Opname'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StockOpnameWidget::class
        ];
    }
}
