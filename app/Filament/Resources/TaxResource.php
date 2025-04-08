<?php

namespace App\Filament\Resources;

use App\Enum\Accounting\TaxType;
use App\Filament\Resources\TaxResource\Pages;
use App\Filament\Resources\TaxResource\RelationManagers;
use App\Models\ChartOfAccount;
use App\Models\Tax;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';
    protected static ?string $navigationGroup = 'Accounting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Pajak')
                            ->required(),
                        Select::make('type')
                            ->label('Tipe')
                            ->options(TaxType::labels())
                            ->required(),
                        TextInput::make('rate')
                            ->label('Rate')
                            ->required()
                            ->numeric(),
                        Select::make('chart_of_account_id')
                            ->label('COA')
                            ->options(ChartOfAccount::pluck('name', 'id')),
                        Select::make('status')
                            ->default(1)
                            ->options([
                                1 => 'Active',
                                0 => 'Non Active',
                            ])
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
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn($state) => TaxType::tryFrom($state)?->label() ?? '-')
                    ->color('info'),
                TextColumn::make('rate')
                    ->sortable()
                    ->suffix(' %'),
                TextColumn::make('coa.name')
                    ->label('COA'),
                ToggleColumn::make('status')
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(TaxType::labels())
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
            'index' => Pages\ManageTaxes::route('/'),
        ];
    }
}
