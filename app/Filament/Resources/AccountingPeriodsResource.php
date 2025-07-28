<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountingPeriodsResource\Pages;
use App\Filament\Resources\AccountingPeriodsResource\RelationManagers;
use App\Models\AccountingPeriods;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountingPeriodsResource extends Resource
{
    protected static ?string $model = AccountingPeriods::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $navigationGroup = 'Master Akuntansi';
    protected static ?string $label = 'Periode Akuntansi';
    protected static ?string $pluralLabel = 'Periode Akuntansi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),
                        Select::make('is_closed')
                            ->options([
                                1 => 'Closed',
                                0 => 'Open',
                            ])
                            ->default(0),
                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date()
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_closed')
                    ->icon(fn(int $state): string => match ($state) {
                        1 => 'heroicon-o-check-circle',
                        0 => 'heroicon-o-x-circle'
                    })
                    ->color(fn(int $state): string => match ($state) {
                        1 => 'success',
                        0 => 'danger',
                    })
            ])
            ->filters([
                SelectFilter::make('is_closed')
                    ->label('Status')
                    ->options([
                        0 => 'Open',
                        1 => 'Closed',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\DeleteAction::make(),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAccountingPeriods::route('/'),
        ];
    }
}
