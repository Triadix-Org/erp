<?php

namespace App\Filament\Resources\DivisionResource\Pages;

use App\Filament\Exports\DivisionExporter;
use App\Filament\Resources\DivisionResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDivisions extends ManageRecords
{
    protected static string $resource = DivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make()
                ->exporter(DivisionExporter::class)
                ->color('info')
        ];
    }
}
