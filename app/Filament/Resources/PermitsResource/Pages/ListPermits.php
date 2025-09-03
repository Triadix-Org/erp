<?php

namespace App\Filament\Resources\PermitsResource\Pages;

use App\Filament\Resources\PermitsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermits extends ListRecords
{
    protected static string $resource = PermitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
