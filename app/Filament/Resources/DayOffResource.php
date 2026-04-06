<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DayOffResource\Pages;
use App\Models\DayOff;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class DayOffResource extends Resource
{
    protected static ?string $model = DayOff::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = 'Human Resource';
    protected static ?string $label = 'Cuti';
    protected static ?string $pluralLabel = 'Cuti';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make()
                            ->columnSpan(1)
                            ->schema([
                                Select::make('lead_id')
                                    ->label('Atasan')
                                    ->relationship('lead', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                DateRangePicker::make('date')
                                    ->label('Tanggal Cuti')
                                    ->required()
                                    ->minDate(now()->addDays(7))
                                    ->displayFormat('d F Y'),
                            ]),
                        Section::make()
                            ->columnSpan(1)
                            ->schema([
                                Textarea::make('reason')
                                    ->label('Alasan (opsional)'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->description(fn(DayOff $record): string => $record->user->employee ? $record->user->employee->nip : '-')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('lead.name')
                    ->label('Atasan')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->formatStateUsing(fn(DayOff $record): string => $record->start_date->format('d F Y') . ' - ' . $record->end_date->format('d F Y'))
                    ->description(fn(DayOff $record): string => 'Total: ' . $record->total_days . ' hari')
                    ->label('Tanggal Cuti'),
                IconColumn::make('is_approved_by_lead')
                    ->label('Disetujui Atasan')
                    ->boolean(),
                IconColumn::make('is_approved_by_hr')
                    ->label('Disetujui HR')
                    ->boolean(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(DayOff $record): string => $record->status->label()),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Action::make('lead_approval')
                        ->label('Setujui Atasan')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->visible(function (DayOff $record): bool {
                            return Auth::user()->getKey() == $record->lead_id && !$record->is_approved_by_lead;
                        })
                        ->action(function (DayOff $record) {
                            $record->is_approved_by_lead = true;
                            $record->save();

                            Notification::make()
                                ->title("Berhasil menyetujui cuti")
                                ->success()
                                ->send();
                        }),
                    Action::make('hr_approval')
                        ->label('Setujui HR')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->visible(function (DayOff $record): bool {
                            return Auth::user()->hasRole('hr') && $record->is_approved_by_lead;
                        })
                        ->action(function (DayOff $record) {
                            $record->is_approved_by_hr = true;
                            $record->status = \App\Enum\HumanResource\DayOffStatus::APPROVED;
                            $record->save();

                            Notification::make()
                                ->title("Berhasil menyetujui cuti")
                                ->success()
                                ->send();
                        }),
                ]),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDayOffs::route('/'),
            'create' => Pages\CreateDayOff::route('/create'),
            'view' => Pages\ViewDayOff::route('/{record}'),
        ];
    }

    public static function setStatus(DayOff $record, \App\Enum\HumanResource\DayOffStatus $status): void
    {
        $record->status = $status;
        $record->save();

        Notification::make()
            ->title("Cuti {$status->label()}")
            ->success()
            ->send();
    }
}
