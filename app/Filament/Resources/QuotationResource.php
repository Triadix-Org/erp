<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Filament\Resources\QuotationResource\RelationManagers;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Tax;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Sales & Marketing';
    protected static ?string $label = 'Penawaran';
    protected static ?string $pluralLabel = 'Penawaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('customer_id')
                                    ->relationship('customer')
                                    ->options(Customer::pluck('name', 'id'))
                                    ->required()
                                    ->searchable(),
                                DatePicker::make('date')
                                    ->required()
                                    ->default(now())
                            ]),
                        TableRepeater::make('detail')
                            ->label('Product/Item')
                            ->relationship('details')
                            ->addActionLabel('Add Product')
                            ->reorderable()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->searchable()
                                    ->options(Product::pluck('name', 'id'))
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                        $product = Product::find($state);
                                        $set('unit_price', $product->price);
                                    })
                                    ->required(),
                                TextInput::make('unit_price')
                                    ->numeric()
                                    ->label('Price/unit')
                                    ->reactive()
                                    ->readOnly(),
                                TextInput::make('qty')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                        $total_price = $get('unit_price') * $state;
                                        $set('total_price', $total_price);
                                        // self::updatedDetails($set, $get);
                                    })
                                    ->numeric(),
                                TextInput::make('total_price')
                                    ->numeric()
                                    ->readOnly()
                                    ->reactive()
                            ])
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::updatedDetails($set, $get);
                            })
                            ->colStyles(function () {
                                return [
                                    'product_id' => 'width: 45%; font-size:12px;',
                                    'unit' => 'width: 15%; font-size:12px;',
                                    'qty' => 'width: 15%; font-size:12px;',
                                    'total_price' => 'width: 20%; font-size:12px;',
                                ];
                            }),
                        Grid::make(2)
                            ->schema([
                                Select::make('tax_id')
                                    ->label('Tax Type')
                                    ->required()
                                    ->options(Tax::pluck('name', 'id'))
                                    ->reactive()
                                    ->searchable()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        $tax = Tax::find($state);
                                        $totalTax = $get('total_amount') * ($tax->rate / 100);
                                        if ($tax) {
                                            $set('total_tax', $totalTax);
                                        } else {
                                            $set('total_tax', 0);
                                        }
                                        self::updatedDetails($set, $get);
                                    }),
                                TextInput::make('total_tax')
                                    ->numeric()
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::updatedDetails($set, $get);
                                    })
                                    ->debounce(1000)
                                    ->default(0),
                                TextInput::make('shipment_price')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->debounce(1000)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        self::updatedDetails($set, $get);
                                    })
                                    ->default(0),
                                TextInput::make('other_price')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->debounce(1000)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        self::updatedDetails($set, $get);
                                    })
                                    ->default(0),
                                TextInput::make('total_amount')
                                    ->numeric()
                                    ->required()
                                    ->default(0),
                            ]),
                    ])
            ]);
    }

    public static function updatedDetails($set, $get)
    {
        $details = collect($get('detail'))->filter(fn($item) => !empty($item['product_id']) && !empty($item['qty']));
        $prices = Product::find($details->pluck('product_id'))->pluck('price', 'id');
        $tax = $get('total_tax') == null ? 0 : $get('total_tax');
        $shipPrice = $get('shipment_price') == null ? 0 : $get('shipment_price');
        $otherPrice = $get('other_price') == null ? 0 : $get('other_price');

        $subtotal = $details->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['product_id']] * $product['qty']);
        }, 0);

        $total = $subtotal + $tax + $shipPrice + $otherPrice;

        $set('total_amount', $total);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipment_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('other_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('status'),
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
                //
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('print')
                        ->label('Print PDF')
                        ->color('info')
                        ->icon('heroicon-o-printer')
                        ->url(fn($record) => env('APP_URL') . '/sales/quotation/pdf/' . $record->code)
                        ->openUrlInNewTab(),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    DeleteAction::make(),
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
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }
}
