<?php

namespace App\Filament\Resources\HeaderRequestOrderResource\Pages;

use App\Filament\Resources\HeaderRequestOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHeaderRequestOrders extends ListRecords
{
    protected static string $resource = HeaderRequestOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
