<?php

namespace App\Filament\Resources\DayOffResource\Pages;

use App\Filament\Resources\DayOffResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDayOff extends CreateRecord
{
    protected static string $resource = DayOffResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [
            'lead_id' => $data['lead_id'],
            'start_date' => $data['date']['start'],
            'end_date' => $data['date']['end'],
            'reason' => $data['reason'] ?? null,
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
