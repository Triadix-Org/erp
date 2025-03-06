<?php

namespace App\Filament\Resources;

use App\Enum\PaymentStatus;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Customer;
use App\Models\HeaderSalesOrder;
use App\Models\Invoice;
use App\Models\Product;
use Carbon\Carbon;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Sales & Marketing';

    public $total_amount = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(7)
                    ->schema([
                        Section::make()
                            ->columnSpan(5)
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        DateTimePicker::make('date')
                                            ->default(Carbon::now('Asia/Jakarta')->locale('id'))
                                            ->required(),
                                        Select::make('customer_id')
                                            ->label('Customer')
                                            ->searchable()
                                            ->options(Customer::pluck('name', 'id')),
                                        Select::make('header_sales_order_id')
                                            ->label('Sales Order')
                                            ->required()
                                            ->searchable()
                                            ->relationship('headerSalesOrder')
                                            ->options(HeaderSalesOrder::pluck('code', 'id'))
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $order = $get('header_sales_order_id');

                                                if ($order) {
                                                    // $details = DetailPurchaseOrder::with('product:id,price')->where('header_purchase_order_id', $headerId)->get();
                                                    $details = HeaderSalesOrder::with('detail:id,header_sales_order_id,product_id,qty', 'detail.product:id,price')->find($order);

                                                    $set('details', $details->detail->map(fn($detail) => [
                                                        'product_id' => $detail->product_id,
                                                        'price' => $detail->product->price,
                                                        'qty' => $detail->qty,
                                                        'price_total' => $detail->product->price * $detail->qty
                                                    ])->toArray());
                                                    self::updatedDetails($set, $get);
                                                } else {
                                                    $set('details', []);
                                                }
                                            }),
                                        DatePicker::make('payment_due')
                                            ->required(),
                                    ]),
                                TableRepeater::make('details')
                                    ->label('Product/Item')
                                    ->relationship('detail')
                                    ->reactive()
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Product')
                                            // ->searchable()
                                            ->options(Product::pluck('name', 'id'))
                                            ->required(),
                                        TextInput::make('price')
                                            ->numeric()
                                            ->dehydrated(false)
                                            ->label('Price/unit')
                                            ->reactive()
                                            ->readOnly(),
                                        TextInput::make('qty')
                                            ->readOnly()
                                            ->numeric(),
                                        TextInput::make('price_total')
                                            ->numeric()
                                            ->readOnly()
                                            ->reactive()
                                    ])
                                    ->reorderable()
                                    ->addable(false)
                                    ->columnSpan('full')
                                    ->colStyles(function () {
                                        return [
                                            'product_id' => 'width: 45%; font-size:12px;',
                                            'price' => 'width: 15%; font-size:12px;',
                                            'qty' => 'width: 15%; font-size:12px;',
                                            'price_total' => 'width: 20%; font-size:12px;',
                                        ];
                                    }),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('total_tax')
                                            ->numeric()
                                            ->reactive()
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                self::updatedDetails($set, $get);
                                            })
                                            ->debounce(1000)
                                            ->default(0),
                                        TextInput::make('total_amount')
                                            ->numeric()
                                            ->required()
                                            ->readOnly()
                                            ->default(0),

                                        Textarea::make('payment_terms')
                                            ->rows(3)
                                            ->required(),
                                        Textarea::make('note')
                                            ->rows(3),
                                    ]),
                            ]),
                        Section::make()
                            ->columnSpan(2)
                            ->schema([
                                FileUpload::make('po')
                                    ->directory('sales/po')
                                    ->label('Purchase 0rder'),
                                Fieldset::make('Shipment Detail')
                                    ->columns(1)
                                    ->schema([
                                        DatePicker::make('ship_date'),
                                        TextInput::make('destination_country')
                                            ->required(),
                                        TextInput::make('port_of_origin'),
                                        TextInput::make('port_of_embarkation'),
                                        TextInput::make('bill_of_lading'),
                                        TextInput::make('total_weight')->numeric()->label('Total weight (Kg)'),
                                        TextInput::make('shipment_price')->numeric()
                                            ->label('Shipment price (Rp.)')
                                            ->default(0)
                                            ->reactive()
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                self::updatedDetails($set, $get);
                                            })
                                    ])
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inv_no')
                    ->searchable()
                    ->sortable()
                    ->label('Inv No'),
                TextColumn::make('date')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->locale('id')->translatedFormat('d F Y h:i')),
                TextColumn::make('customer.name'),
                TextColumn::make('ship_date')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->locale('id')->translatedFormat('d F Y')),
                TextColumn::make('destination_country'),
                TextColumn::make('headerSalesOrder.code')
                    ->label('Sales order'),
                TextColumn::make('payment_status')
                    ->badge()
                    ->formatStateUsing(fn($state) => PaymentStatus::tryFrom($state)?->label() ?? '-')
                    ->color(fn($state) => PaymentStatus::tryFrom($state)?->color() ?? 'gray'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->tooltip('Actions'),
                Action::make('openModal')
                    ->label('Documents')
                    ->icon('heroicon-s-document')
                    ->modalContent(function ($record) {
                        $code = $record->inv_no;
                        return view('filament.pages.modal', compact('code'));
                    })
                    ->button()
                    ->modalCancelActionLabel('Close')
                    ->modalHeading(function ($record) {
                        return 'Documents - ' . $record->inv_no;
                    })
                    ->modalSubmitAction(false)


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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function updatedDetails($set, $get)
    {
        $details = collect($get('details'));
        $tax = $get('total_tax');
        $shipPrice = $get('shipment_price');

        $totalPrice = $details->reduce(function ($total, $line) {
            return $total + $line['price_total'];
        }, 0);

        $totalAmount = $totalPrice + $tax + $shipPrice;

        $set('total_amount', $totalAmount);
    }
}
