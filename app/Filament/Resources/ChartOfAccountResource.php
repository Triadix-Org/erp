<?php

namespace App\Filament\Resources;

use App\Enum\Accounting\CoaType;
use App\Filament\Resources\ChartOfAccountResource\Pages;
use App\Filament\Resources\ChartOfAccountResource\RelationManagers;
use App\Models\ChartOfAccount;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class ChartOfAccountResource extends Resource
{
    protected static ?string $model = ChartOfAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Accounting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode COA')
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),
                        Select::make('type')
                            ->label('Tipe Akun')
                            ->options(CoaType::labels())
                            ->required(),
                        Select::make('chart_of_account_id')
                            ->label('Turunan dari')
                            ->options(ChartOfAccount::pluck('name', 'id')),
                        Textarea::make('description')
                            ->columnSpanFull()
                            ->label('Deskripsi')
                            ->rows(4),
                        Checkbox::make('is_active')
                            ->label('Aktif?')
                            ->default(0)
                            ->dehydrated(true)
                            ->afterStateHydrated(function ($component, $state) {
                                $component->state((bool) $state);
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('is_active', $state ? 1 : 0);
                            }),
                        Checkbox::make('is_contra')
                            ->label('Akun Kontra')
                            ->default(0)
                            ->dehydrated(true)
                            ->afterStateHydrated(function ($component, $state) {
                                $component->state((bool) $state);
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('is_contra', $state ? 1 : 0);
                            })
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable()->label('Kode'),
                TextColumn::make('name')->searchable()->label('Nama Akun'),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn($state) => CoaType::tryFrom($state)?->label() ?? '-')
                    ->color('info'),
                TextColumn::make('parent.name')->label('Turunan')
                    ->badge()
                    ->color('primary'),
                ToggleColumn::make('is_active')->label('Aktif?'),
                ToggleColumn::make('is_contra')->label('Akun Kontra'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(CoaType::labels()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\DeleteAction::make(),
            ], position: ActionsPosition::BeforeCells)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageChartOfAccounts::route('/'),
        ];
    }
}
