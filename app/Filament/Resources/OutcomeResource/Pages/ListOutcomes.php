<?php

namespace App\Filament\Resources\OutcomeResource\Pages;

use App\Filament\Exports\OutcomeExporter;
use App\Filament\Resources\OutcomeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\ExportAction;

class ListOutcomes extends ListRecords
{
    protected static string $resource = OutcomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make('export')
                ->exporter(OutcomeExporter::class)
                ->icon('heroicon-s-document-text')
                ->color('info')
        ];
    }
}
