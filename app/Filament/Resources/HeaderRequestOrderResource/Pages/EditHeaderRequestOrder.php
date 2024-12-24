<?php

namespace App\Filament\Resources\HeaderRequestOrderResource\Pages;

use App\Filament\Resources\HeaderRequestOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHeaderRequestOrder extends EditRecord
{
    protected static string $resource = HeaderRequestOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
