<?php

namespace App\Filament\Resources\AccountingPeriodsResource\Pages;

use App\Filament\Resources\AccountingPeriodsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAccountingPeriods extends ManageRecords
{
    protected static string $resource = AccountingPeriodsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Buat Periode'),
        ];
    }
}
