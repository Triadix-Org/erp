<?php

namespace App\Filament\Resources\PermitsResource\Pages;

use App\Filament\Resources\PermitsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermits extends EditRecord
{
    protected static string $resource = PermitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
