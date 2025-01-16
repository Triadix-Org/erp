<?php

namespace App\Filament\Resources\HeaderMaterialReceivedResource\Pages;

use App\Filament\Resources\HeaderMaterialReceivedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHeaderMaterialReceived extends EditRecord
{
    protected static string $resource = HeaderMaterialReceivedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
