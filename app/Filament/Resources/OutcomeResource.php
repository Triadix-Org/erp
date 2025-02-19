<?php

namespace App\Filament\Resources;

use App\Enum\OutcomeType;
use App\Filament\Resources\OutcomeResource\Pages;
use App\Filament\Resources\OutcomeResource\RelationManagers;
use App\Filament\Resources\ProductResource\Widgets\Product as ProductWidget;
use App\Filament\Resources\OutcomeResource\Widgets\Outcome as WidgetsOutcome;
use App\Models\Outcome;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OutcomeResource extends Resource
{
    protected static ?string $model = Outcome::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make()
                            ->columnSpan(1)
                            ->schema([
                                DatePicker::make('date')
                                    ->default(now())
                                    ->required(),
                                TextInput::make('amount')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Select::make('type')
                                    ->options(OutcomeType::labels())
                                    ->required(),
                                TextInput::make('pay_to')
                                    ->maxLength(255),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn($state) => OutcomeType::tryFrom($state)?->label() ?? '-')
                    ->color('info'),
                Tables\Columns\TextColumn::make('pay_to')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    })
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->color('info'),
                    Tables\Actions\EditAction::make(),
                    DeleteAction::make()
                ])
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            ProductWidget::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOutcomes::route('/'),
            'create' => Pages\CreateOutcome::route('/create'),
            'edit' => Pages\EditOutcome::route('/{record}/edit'),
        ];
    }
}
