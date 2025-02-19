<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductResource\Widgets\Product;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make()
                ->exporter(ProductExporter::class)
                ->color('info')
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Product::class,
        ];
    }
}
