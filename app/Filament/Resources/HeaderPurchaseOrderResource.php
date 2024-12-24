<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeaderPurchaseOrderResource\Pages;
use App\Filament\Resources\HeaderPurchaseOrderResource\RelationManagers;
use App\Models\DetailRequestOrder;
use App\Models\HeaderPurchaseOrder;
use App\Models\HeaderRequestOrder;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class HeaderPurchaseOrderResource extends Resource
{
    protected static ?string $model = HeaderPurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Purchase Order';
    protected static ?string $navigationGroup = 'Purchasing';
    protected static ?string $label = 'Purchase Order';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('po_date')
                    ->default(Carbon::now())
                    ->required(),
                Forms\Components\TextInput::make('purchaser')
                    ->default(Auth::user()->email)
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier')
                    ->label('Vendor/Supplier')
                    ->options(Supplier::where('status', 1)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('header_request_order_id')
                    ->relationship('req_order')
                    ->label('Request Order')
                    ->options(HeaderRequestOrder::where('status', 1)->pluck('code', 'id'))
                    ->reactive()
                    ->searchable()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        self::updateTotals($get, $set);
                        $headerRequestOrderId = $get('header_request_order_id');

                        if ($headerRequestOrderId) {
                            $details = DetailRequestOrder::with('product:id,price')->where('header_request_order_id', $headerRequestOrderId)->get();
                            // dd($details);

                            $set('details', $details->map(fn($detail) => [
                                $total = $detail->product->price * $detail->qty,
                                'product_id' => $detail->product_id,
                                'price' => $detail->product->price,
                                'qty' => $detail->qty,
                                'total' => $total,
                                'total_price_line' => $total
                            ])->toArray());
                        } else {
                            $set('details', []);
                        }
                    }),
                Forms\Components\Textarea::make('payment_terms')
                    ->cols(3),
                Forms\Components\Textarea::make('incoterms')
                    ->cols(3),
                TableRepeater::make('details')
                    ->relationship('detail')
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->reactive()
                            ->relationship(
                                name: 'item',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('status', 1),
                            )
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $productId = $get('product_id');
                                if ($productId) {
                                    $product = Product::where('id', $productId)->first();
                                    $set('price', $product->price);
                                }
                            })
                            ->required(),

                        TextInput::make('price')
                            ->prefix('Rp.')
                            ->label('Price/unit')
                            ->numeric()
                            ->readOnly(),

                        TextInput::make('qty')
                            ->numeric()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $qty = $get('qty');
                                $price = $get('price');
                                if ($qty) {
                                    $total = $qty * $price;
                                    $set('total_price_line', $total);
                                    $set('total', $total);
                                }
                            })
                            ->reactive(),

                        TextInput::make('total_price_line')
                            ->prefix('Rp.')
                            ->label('Price')
                            ->dehydrated()
                            ->numeric()
                            ->readOnly(),

                        Checkbox::make('tax')
                            ->label('Tax')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($state == true) {
                                    $price = $get('price');
                                    $qty = $get('qty');
                                    $taxAmount = 0.12 * ($price * $qty);
                                    $set('tax_rp', $taxAmount);
                                    $totalPrice = ($price * $qty) + $taxAmount;
                                    $set('total', $totalPrice);
                                } else {
                                    $price = $get('price');
                                    $qty = $get('qty');
                                    $taxAmount = 0;
                                    $set('tax_rp', $taxAmount);
                                    $totalPrice = ($price * $qty) - $taxAmount;
                                    $set('total', $totalPrice);
                                }

                                // self::updateTotals($get, $set);
                            }),
                        TextInput::make('tax_rp')
                            ->label('Tax Amount')
                            ->prefix('Rp.')
                            ->default(0)
                            ->numeric()
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            ->readOnly(),
                        TextInput::make('total')
                            ->prefix('Rp.')
                            ->numeric()
                            ->readOnly(),

                    ])
                    ->reorderable()
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotals($get, $set);
                    })
                    ->columnSpan('full'),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_tax')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_disc')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('po_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchaser')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('header_request_order_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_terms')
                    ->searchable(),
                Tables\Columns\TextColumn::make('incoterms')
                    ->searchable(),
                Tables\Columns\TextColumn::make('app_operational')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('operational_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('app_finance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finance_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vendor_confirm')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_tax')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_disc')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
            ])
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
            'index' => Pages\ListHeaderPurchaseOrders::route('/'),
            'create' => Pages\CreateHeaderPurchaseOrder::route('/create'),
            'edit' => Pages\EditHeaderPurchaseOrder::route('/{record}/edit'),
        ];
    }

    public static function updateTotals(Get $get, Set $set)
    {
        // dd('ada');
        $selectedProducts = collect($get('details'))->filter(fn($productId) => !empty($productId['product_id']));
        // $prices = $get('price');
        // dd($selectedProducts);

        $asubtotal = $selectedProducts->reduce(function ($subtotal, $product) {
            return $subtotal + ($product['price'] * $product['qty']);
        }, 0);
        // dd($asubtotal);

        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) {
            return $subtotal + ($product['price'] * $product['qty']) + $product['tax_rp'];
        }, 0);

        $set('subtotal', $asubtotal);
        $set('total_amount', $subtotal);
    }
}
