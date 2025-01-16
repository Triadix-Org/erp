<?php

namespace App\Filament\Resources\HeaderSalesOrderResource\Pages;

use App\Filament\Resources\HeaderSalesOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHeaderSalesOrders extends ManageRecords
{
    protected static string $resource = HeaderSalesOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('5xl'),
        ];
    }
}
