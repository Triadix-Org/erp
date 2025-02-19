<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use App\Filament\Resources\IncomeResource\Widgets\IncomeWidget;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageIncomes extends ManageRecords
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IncomeWidget::class
        ];
    }
}
