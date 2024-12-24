<?php

namespace App\Filament\Resources\HeaderPurchaseOrderResource\Pages;

use App\Filament\Resources\HeaderPurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHeaderPurchaseOrders extends ListRecords
{
    protected static string $resource = HeaderPurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
