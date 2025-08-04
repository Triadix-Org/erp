<?php

namespace App\Filament\Resources;

use App\Enum\Accounting\JournalSource;
use App\Enum\PaymentStatus;
use App\Filament\Resources\HeaderPurchaseOrderResource\Pages;
use App\Filament\Resources\HeaderPurchaseOrderResource\RelationManagers;
use App\Models\AccountingPeriods;
use App\Models\ChartOfAccount;
use App\Models\DetailJournalEntry;
use App\Models\DetailRequestOrder;
use App\Models\HeaderPurchaseOrder;
use App\Models\HeaderRequestOrder;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tax;
use App\Services\PostingJournal;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
use Filament\Tables\Filters\SelectFilter;
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
                Section::make()
                    ->schema([
                        Grid::make(3)
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
                                    ->options(HeaderRequestOrder::active()->approved()->pluck('code', 'id'))
                                    ->label('Request Order')
                                    ->reactive()
                                    ->searchable()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $headerRequestOrderId = $get('header_request_order_id');

                                        if ($headerRequestOrderId) {
                                            $details = DetailRequestOrder::with('product:id,price')->where('header_request_order_id', $headerRequestOrderId)->get();

                                            $set('details', $details->map(fn($detail) => [
                                                $total = $detail->product->price * $detail->qty,
                                                'product_id' => $detail->product_id,
                                                'price' => $detail->product->price,
                                                'qty' => $detail->qty,
                                                'total' => $total
                                            ])->toArray());
                                            self::updateTotals($get, $set);
                                        } else {
                                            $set('details', []);
                                        }
                                    }),
                                DatePicker::make('payment_due')
                                    ->required(),
                                Textarea::make('payment_terms')
                                    ->cols(3),
                                Textarea::make('incoterms')
                                    ->cols(3),
                            ]),
                        TableRepeater::make('details')
                            ->relationship('detail')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->reactive()
                                    ->preload()
                                    ->optionsLimit(6)
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
                                    ->searchable()
                                    ->required(),

                                TextInput::make('price')
                                    ->prefix('Rp.')
                                    ->label('Price/unit')
                                    ->numeric()
                                    ->dehydrated(false)
                                    ->readOnly(),

                                TextInput::make('qty')
                                    ->numeric()
                                    ->debounce(1000)
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
                                TextInput::make('total')
                                    ->prefix('Rp.')
                                    ->numeric()
                                    ->readOnly(),

                            ])
                            ->reorderable()
                            ->addable()
                            ->columnSpan('full'),
                        Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('subtotal')
                                    ->required()
                                    ->readOnly()
                                    ->numeric()
                                    ->default(0),
                                Select::make('tax_id')
                                    ->label('Tax Type')
                                    ->required()
                                    ->options(Tax::pluck('name', 'id'))
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                        $subtotal = $get('subtotal');
                                        $taxRate = Tax::find($state)->rate;

                                        $totalTax = ($subtotal * $taxRate) / 100;
                                        $set('total_tax', $totalTax);

                                        self::updateTotals($get, $set);
                                    })
                                    ->searchable(),
                                Forms\Components\TextInput::make('total_disc')
                                    ->required()
                                    ->label('Discount')
                                    ->numeric()
                                    ->reactive()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateTotals($get, $set);
                                    })
                                    ->debounce(1000)
                                    ->default(0),
                                Forms\Components\TextInput::make('total_tax')
                                    ->required()
                                    ->readOnly()
                                    ->debounce(1000)
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('total_amount')
                                    ->required()
                                    ->numeric()
                                    ->readOnly()
                                    ->default(0),
                            ])
                    ])
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
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->formatStateUsing(fn($state) => PaymentStatus::tryFrom($state)?->label() ?? '-')
                    ->color(fn($state) => PaymentStatus::tryFrom($state)?->color() ?? 'gray'),
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
                SelectFilter::make('payment_status')
                    ->options(PaymentStatus::labels())
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
                    Action::make('postingJournal')
                        ->label('Posting ke Jurnal')
                        ->color('info')
                        ->modalWidth('7xl')
                        ->modalDescription('Tindakan ini akan menambah Jurnal Entri dan status entri adalah Posted.')
                        ->form([
                            Grid::make(3)
                                ->schema([
                                    DatePicker::make('date')
                                        ->required()
                                        ->default(now())
                                        ->label('Tanggal'),
                                    Select::make('accounting_periods')
                                        ->label('Periode')
                                        ->options(AccountingPeriods::open()->pluck('name', 'id'))
                                        ->searchable()
                                        ->required(),
                                    TextInput::make('header_description')
                                        ->label('Catatan'),
                                ]),
                            TableRepeater::make('details')
                                ->label(false)
                                ->schema([
                                    Select::make('chart_of_account_id')
                                        ->label('CoA')
                                        ->required()
                                        ->options(ChartOfAccount::pluck('name', 'id'))
                                        ->default(8)
                                        ->searchable(),
                                    TextInput::make('description')
                                        ->label('Keterangan'),
                                    TextInput::make('debit')
                                        ->numeric(),
                                    TextInput::make('kredit')
                                        ->numeric(),
                                ])
                                ->colStyles([
                                    'debit' => 'width: 15%;',
                                    'kredit' => 'width: 15%;',
                                ])
                        ])
                        ->action(function (array $data, HeaderPurchaseOrder $record) {
                            $posting = new PostingJournal();
                            $posting($data, $record, JournalSource::PO->value);
                        })
                        ->slideOver()
                    // ->visible(fn($record) => $record->app_finance == 1 &&
                    //     (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Finance'))),
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

    public static function updateTotals($get, $set)
    {
        $details = collect($get('details'));
        $tax = $get('total_tax');
        $disc = $get('total_disc');

        $totalPrice = $details->reduce(function ($total, $line) {
            return $total + $line['total'];
        }, 0);

        $subtotal = $totalPrice + $tax - $disc;

        $set('subtotal', $totalPrice);
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
