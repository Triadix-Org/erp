<?php

namespace App\Filament\Resources;

use App\Enum\IncomeType;
use App\Filament\Resources\IncomeResource\Pages;
use App\Filament\Resources\IncomeResource\RelationManagers;
use App\Filament\Resources\IncomeResource\Widgets\IncomeWidget;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $label = 'Pendapatan';
    protected static ?string $pluralLabel = 'Pendapatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        DatePicker::make('date')
                            ->required()
                            ->default(now()),
                        TextInput::make('amount')
                            ->required()
                            ->numeric(),
                        Select::make('type')
                            ->required()
                            ->options(IncomeType::labels()),
                        TextInput::make('from')
                            ->required()
                            ->label('Income From')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->label('Amount (Rp.)')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn($state) => IncomeType::tryFrom($state)?->label() ?? '-')
                    ->color('info'),
                TextColumn::make('from')
                    ->searchable()
            ])
            ->filters([
                Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn(Builder $query) => $query->whereBetween('date', [
                        Carbon::now()->startOfMonth()->toDateString(),
                        Carbon::now()->endOfMonth()->toDateString(),
                    ]))
                    ->default(),

                Filter::make('date')
                    ->form([
                        Section::make('Date Range')
                            ->schema([
                                DatePicker::make('date_from')->label('Date From'),
                                DatePicker::make('date_until')->label('Date Until'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
                SelectFilter::make('type')
                    ->options(IncomeType::labels())
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->color('info'),
                    Tables\Actions\EditAction::make()->color('warning'),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ManageIncomes::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            IncomeWidget::class
        ];
    }
}
