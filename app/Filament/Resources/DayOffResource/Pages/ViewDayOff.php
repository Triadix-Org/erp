<?php

namespace App\Filament\Resources\DayOffResource\Pages;

use App\Filament\Resources\DayOffResource;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Resources\Pages\ViewRecord;

class ViewDayOff extends ViewRecord
{
    protected static string $resource = DayOffResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make('Informasi Karyawan')
                            ->columnSpan(1)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name')
                                    ->inlineLabel()
                                    ->formatStateUsing(fn() => ': ' . $this->record->user->name)
                                    ->label('Nama Karyawan'),
                                Infolists\Components\TextEntry::make('user.employee.nip')
                                    ->inlineLabel()
                                    ->formatStateUsing(fn($state) => ': ' . $state)
                                    ->label('NIP'),
                                Infolists\Components\TextEntry::make('user.employee.personnel.position')
                                    ->inlineLabel()
                                    ->formatStateUsing(fn($state) => ': ' . $state)
                                    ->label('Jabatan'),
                                Infolists\Components\TextEntry::make('user.employee.personnel.division.name')
                                    ->inlineLabel()
                                    ->formatStateUsing(fn($state) => ': ' . $state)
                                    ->label('Divisi'),
                                Infolists\Components\TextEntry::make('user.employee.personnel.department.name')
                                    ->inlineLabel()
                                    ->formatStateUsing(fn($state) => ': ' . $state)
                                    ->label('Departemen'),
                            ]),
                        Section::make('Detail Cuti')
                            ->columnSpan(1)
                            ->schema([
                                Infolists\Components\TextEntry::make('lead.name')
                                    ->inlineLabel()
                                    ->label('Atasan')
                                    ->formatStateUsing(fn() => ': ' . $this->record->lead->name),
                                Infolists\Components\TextEntry::make('start_date', 'end_date')
                                    ->inlineLabel()
                                    ->label('Tanggal Cuti')
                                    ->formatStateUsing(fn() => ': ' . $this->record->start_date->format('d F Y') . ' - ' . $this->record->end_date->format('d F Y')),
                                Infolists\Components\TextEntry::make('total_days')
                                    ->inlineLabel()
                                    ->label('Total Hari Cuti')
                                    ->formatStateUsing(fn($state) => ': ' . $state . ' hari'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->inlineLabel()
                                    ->label('Tanggal Pengajuan')
                                    ->formatStateUsing(fn($state) => ': ' . $state->format('d F Y')),
                                Infolists\Components\TextEntry::make('reason')
                                    ->inlineLabel()
                                    ->label('Alasan')
                                    ->formatStateUsing(fn($state) => ': ' . ($state ?? '-'))
                            ]),
                    ])
            ]);
    }
}
