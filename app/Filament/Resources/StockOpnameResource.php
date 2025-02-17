<?php

namespace App\Filament\Resources;

use App\Enum\StockOpnameStatus;
use App\Filament\Resources\StockOpnameResource\Pages;
use App\Filament\Resources\StockOpnameResource\RelationManagers;
use App\Jobs\UpdateStockProduct;
use App\Models\DetailStockOpname;
use App\Models\Product;
use App\Models\StockOpname;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class StockOpnameResource extends Resource
{
    protected static ?string $model = StockOpname::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Warehouse';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        DatePicker::make('date')
                            ->default(now())
                            ->required(),
                        Select::make('warehouse_id')
                            ->label('Warehouse')
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->live()
                            ->afterStateUpdated(fn(Set $set, $state) => $set('product_id', null))
                            ->options(fn() => Warehouse::pluck('name', 'id'))
                        // ->afterStateUpdated(function (callable $set) {
                        //     $set('product_id', null);
                        // }),
                    ]),
                Section::make('Product')
                    ->columns(1)
                    ->schema([
                        TableRepeater::make('detail')
                            ->relationship('detail')
                            ->label(false)
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(
                                        function (Get $get) {
                                            // $warehouseId = $get('warehouse_id');
                                            // if ($warehouseId) {
                                            //     return Product::isActive()->where('warehouse_id', $warehouseId)->get()->pluck('name', 'id');
                                            // }
                                            // return [];
                                            return Product::isActive()->get()->pluck('name', 'id');
                                        }
                                    )
                                    ->required()
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('stock_system', $product->stock);
                                        }
                                    }),
                                TextInput::make('stock_system')
                                    ->label('Stock in System')
                                    ->readOnly()
                                    ->numeric(),
                                TextInput::make('stock_actual')
                                    ->label('Stock Actual')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->debounce(500)
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                        $stockInSystem = $get('stock_system');

                                        if ($stockInSystem && $state) {
                                            if ($stockInSystem >= $state) {
                                                $gap = $stockInSystem - $state;
                                            } else {
                                                $gap = $state - $stockInSystem;
                                            }
                                            $set('gap', $gap);
                                        }
                                    }),
                                TextInput::make('gap')
                                    ->label('Gap')
                                    ->numeric()
                                    ->readOnly(),
                                TextInput::make('description')
                            ])
                            ->colStyles(function () {
                                return [
                                    'product_id' => 'width: 30%;',
                                    'stock_system' => 'width: 15%;',
                                    'stock_actual' => 'width: 15%;',
                                    'gap' => 'width: 15%;',
                                    'description' => 'width: 25%;',
                                ];
                            })
                            ->reorderable(false)
                            ->cloneable()
                            ->collapsible()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => StockOpnameStatus::tryFrom($state)?->label() ?? '-')
                    ->color(fn($state) => StockOpnameStatus::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('approval.name')
                    ->label('Approved_by')
                    ->searchable(),
                TextColumn::make('approved_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('approve')
                        ->icon('heroicon-m-document-check')
                        ->color('primary')
                        ->visible(fn($record) => $record->status == 0)
                        ->action(function ($record) {
                            self::setStatus($record, 3);
                        })
                        ->requiresConfirmation(),
                    ViewAction::make()
                        ->color('info'),
                    EditAction::make()
                        ->color('warning')
                        ->visible(fn($record) => $record->status == 0 | $record->status == 2),
                    DeleteAction::make()
                        ->color('danger')
                        ->visible(fn($record) => $record->status == 0 | $record->status == 2),
                ])
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
            'index' => Pages\ListStockOpnames::route('/'),
            'create' => Pages\CreateStockOpname::route('/create'),
        ];
    }

    public static function setStatus(StockOpname $record, $status)
    {
        try {
            DB::beginTransaction();

            if ($status == 3) {
                $record->update([
                    'status' => $status,
                    'approved_by' => Auth::user()->id,
                    'approved_at' => now()
                ]);

                self::updateStock($record);
            }

            DB::commit();
            Notification::make()
                ->success()
                ->title('Saved')
                ->body('Stock Opname Successfully Approved')
                ->send();
        } catch (Throwable $th) {
            DB::rollBack();
            Notification::make()
                ->danger()
                ->title('Oppss!')
                ->body('500 Server Error')
                ->body($th->getMessage())
                ->send();
        }
    }

    public static function updateStock(StockOpname $record)
    {
        DetailStockOpname::where('stock_opname_id', $record->getKey())->select('id', 'product_id', 'stock_actual')
            ->chunk(
                100,
                function ($details) {
                    foreach ($details as $detail) {
                        $product = Product::find($detail['product_id']);
                        $product->stock = $detail['stock_actual'];
                        $product->save();
                    }
                }
            );
    }
}
