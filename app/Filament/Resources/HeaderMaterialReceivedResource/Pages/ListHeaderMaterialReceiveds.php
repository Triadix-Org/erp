<?php

namespace App\Filament\Resources\HeaderMaterialReceivedResource\Pages;

use App\Filament\Resources\HeaderMaterialReceivedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHeaderMaterialReceiveds extends ListRecords
{
    protected static string $resource = HeaderMaterialReceivedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
