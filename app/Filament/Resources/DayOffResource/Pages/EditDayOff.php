<?php

namespace App\Filament\Resources\DayOffResource\Pages;

use App\Filament\Resources\DayOffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDayOff extends EditRecord
{
    protected static string $resource = DayOffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
