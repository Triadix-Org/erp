<?php

namespace App\Filament\Resources;

use App\Enum\Month;
use App\Filament\Resources\PayrollResource\Pages;
use App\Filament\Resources\PayrollResource\RelationManagers;
use App\Models\Employee;
use App\Models\Payroll;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Human Resource';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('month')
                                    ->required()
                                    ->options(Month::labels())
                                    ->searchable(),
                                TextInput::make('year')
                                    ->required()
                                    ->numeric(),
                                TableRepeater::make('detail')
                                    ->addable(false)
                                    ->schema([
                                        TextInput::make('employee_id')
                                            ->label('ID')
                                            ->readOnly(),
                                        TextInput::make('name')
                                            ->readOnly()
                                            ->dehydrated(false),
                                        TextInput::make('salary')
                                            ->readOnly()
                                            ->numeric()
                                            ->prefix('Rp.'),
                                        TextInput::make('overtime')
                                            ->default(0)
                                            ->numeric()
                                            ->prefix('Rp.')
                                            ->reactive()
                                            ->debounce(500)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::calculateAmount($get, $set);
                                            }),
                                        TextInput::make('bonus')
                                            ->default(0)
                                            ->numeric()
                                            ->prefix('Rp.')
                                            ->reactive()
                                            ->debounce(500)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::calculateAmount($get, $set);
                                            }),
                                        TextInput::make('cut')
                                            ->label('Deduction')
                                            ->default(0)
                                            ->numeric()
                                            ->prefix('Rp.')
                                            ->reactive()
                                            ->debounce(500)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::calculateAmount($get, $set);
                                            }),
                                        TextInput::make('total')
                                            ->default(0)
                                            ->numeric()
                                            ->prefix('Rp.')
                                    ])
                                    ->colStyles(function () {
                                        return [
                                            'employee_id' => 'width: 5%;',
                                        ];
                                    })
                            ])
                    ])
            ]);
    }

    public static function calculateAmount($get, $set)
    {
        $salary = $get('salary');
        $overtime = $get('overtime') ?? 0;
        $bonus = $get('bonus') ?? 0;
        $cut = $get('cut') ?? 0;

        $total = $salary + $overtime + $bonus - $cut;
        $set('total', $total);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('month')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year')
                    ->searchable()
                    ->sortable()
            ])
            ->filters([
                SelectFilter::make('month')
                    ->options(Month::labels()),
                Filter::make('year')
                    ->form([
                        TextInput::make('years')
                            ->type('month')
                    ])
            ])
            ->actions([
                ViewAction::make()->color('info'),
                EditAction::make()->color('warning'),
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
            'index' => Pages\ListPayrolls::route('/'),
            'create' => Pages\CreatePayroll::route('/create'),
            'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
