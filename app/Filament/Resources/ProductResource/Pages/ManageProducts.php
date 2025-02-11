<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductResource\Widgets\Product;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('stockOpname')
                ->label('Stock Opname/Adjustment')
                ->url(route('filament.root.resources.products.stock-opname'))
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Product::class,
        ];
    }
}
