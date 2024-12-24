<?php

namespace App\Filament\Resources\HeaderPurchaseOrderResource\Pages;

use App\Filament\Resources\HeaderPurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHeaderPurchaseOrder extends EditRecord
{
    protected static string $resource = HeaderPurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
