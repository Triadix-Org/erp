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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

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
                    ->readOnly()
                    ->maxLength(255),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name', function ($query) {
                        return $query->where('status', 1);
                    })
                    ->label('Vendor/Supplier')
                    ->options(Supplier::where('status', 1)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('header_request_order_id')
                    ->relationship('header_request_order', 'code', function ($query) {
                        return $query->where('status', 1);
                    })
                    ->options(HeaderRequestOrder::where('status', 1)->pluck('code', 'id'))
                    ->label('Request Order')
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
                                name: 'product',
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
                            ->dehydrated(false)
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
                            ->dehydrated(false)
                            ->numeric()
                            ->readOnly(),

                        Checkbox::make('tax')
                            ->label('Tax')
                            ->reactive()
                            ->dehydrated(false)
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
                            // ->afterStateUpdated(function (Get $get, Set $set) {
                            //     self::updateTotals($get, $set);
                            // })
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
                Tables\Columns\TextColumn::make('supplier.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('header_request_order.code')
                    ->label('Req Number'),
                Tables\Columns\TextColumn::make('payment_terms')
                    ->searchable(),
                Tables\Columns\TextColumn::make('incoterms')
                    ->searchable(),
                Tables\Columns\TextColumn::make('app_operational')
                    ->label('Approval Operational')
                    ->badge()
                    ->colors([
                        'success' => fn($state): bool => $state == 1,
                        'danger' => fn($state): bool => $state == 0,
                    ])
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Approved' : 'Not Approved'),
                Tables\Columns\TextColumn::make('operational_by')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('app_finance')
                    ->label('Approval Finance')
                    ->badge()
                    ->colors([
                        'success' => fn($state): bool => $state == 1,
                        'danger' => fn($state): bool => $state == 0,
                    ])
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Approved' : 'Not Approved'),
                Tables\Columns\TextColumn::make('finance_by')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('vendor_confirm')
                    ->label('Vendor Confirm')
                    ->badge()
                    ->colors([
                        'success' => fn($state): bool => $state == 1,
                        'danger' => fn($state): bool => $state == 0,
                    ])
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Confirmed' : 'Not Confirmed')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => fn($state): bool => $state == 1,
                        'warning' => fn($state): bool => $state == 0,
                    ])
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Active' : 'Non Active'),
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
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('print')
                        ->label('Print PDF')
                        ->color('info')
                        ->icon('heroicon-o-printer')
                        ->url(fn($record) => env('APP_URL') . '/purchasing/purchase-order/pdf/' . $record->code)
                        ->openUrlInNewTab(),
                    Action::make('setStatusOperational1')
                        ->label('Approve Operational')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Confirmation')
                        ->modalDescription('Are you sure you want to approve?')
                        ->action(function ($record) {
                            $num = $record->code;
                            self::setStatusOperational($num, 1);
                        })
                        ->visible(fn($record) => $record->app_operational == 0 &&
                            (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Operational Manager'))),
                    Action::make('setStatusOperational0')
                        ->label('Cancle Approve Operational')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Confirmation')
                        ->modalDescription('Are you sure you want to cancle approve?')
                        ->action(function ($record) {
                            $num = $record->code;
                            self::setStatusOperational($num, 0);
                        })
                        ->visible(fn($record) => $record->app_operational == 1 &&
                            (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Operational Manager'))),
                    Action::make('setStatusFinance1')
                        ->label('Approve Finance')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Confirmation')
                        ->modalDescription('Are you sure you want to approve?')
                        ->action(function ($record) {
                            $num = $record->code;
                            self::setStatusFinance($num, 1);
                        })
                        ->visible(fn($record) => $record->app_finance == 0 &&
                            (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Finance'))),
                    Action::make('setStatusFinance0')
                        ->label('Cancle Approve Finance')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Confirmation')
                        ->modalDescription('Are you sure you want to cancle approve?')
                        ->action(function ($record) {
                            $num = $record->code;
                            self::setStatusFinance($num, 0);
                        })
                        ->visible(fn($record) => $record->app_finance == 1 &&
                            (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Finance'))),
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
            return $subtotal + ($product['price'] * $product['qty']);
        }, 0);

        $set('subtotal', $asubtotal);
        $set('total_amount', $subtotal);
    }

    public static function setStatusOperational($num, $status)
    {
        try {
            DB::beginTransaction();

            $record = HeaderPurchaseOrder::where('code', $num)->first();

            if ($status == 0) {
                $record->app_operational = 0;
                $record->operational_by  = null;
                $record->save();
            } else {
                $record->app_operational = 1;
                $record->operational_by  = Auth::user()->email;
                $record->save();
            }

            DB::commit();
            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();
        } catch (Throwable $th) {
            DB::rollBack();
            Notification::make()
                ->title('Opps.. Something went wrong!')
                ->body($th->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function setStatusFinance($num, $status)
    {
        try {
            DB::beginTransaction();

            $record = HeaderPurchaseOrder::where('code', $num)->first();

            if ($status == 0) {
                $record->app_finance = 0;
                $record->finance_by  = null;
                $record->save();
            } else {
                $record->app_finance = 1;
                $record->finance_by  = Auth::user()->email;
                $record->save();
            }

            DB::commit();
            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();
        } catch (Throwable $th) {
            DB::rollBack();
            Notification::make()
                ->title('Opps.. Something went wrong!')
                ->body($th->getMessage())
                ->danger()
                ->send();
        }
    }
}
