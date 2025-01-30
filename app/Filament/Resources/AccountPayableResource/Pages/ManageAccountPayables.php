<?php

namespace App\Filament\Resources\AccountPayableResource\Pages;

use App\Filament\Resources\AccountPayableResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAccountPayables extends ManageRecords
{
    protected static string $resource = AccountPayableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
