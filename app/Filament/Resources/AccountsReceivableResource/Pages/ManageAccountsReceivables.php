<?php

namespace App\Filament\Resources\AccountsReceivableResource\Pages;

use App\Filament\Resources\AccountsReceivableResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAccountsReceivables extends ManageRecords
{
    protected static string $resource = AccountsReceivableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\AccountReceivableResource\Widgets\AccountReceivableStats::class,
        ];
    }
}
