<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Customer;
use App\Models\HeaderSalesOrder;
use App\Models\Invoice;
use App\Models\Product;
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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Sales & Marketing';

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
                                            ->required(),
                                        Select::make('customer_id')
                                            ->label('Customer')
                                            ->searchable()
                                            ->options(Customer::pluck('name', 'id')),
                                        Select::make('header_sales_order_id')
                                            ->label('Sales Order')
                                            ->required()
                                            ->searchable()
                                            ->options(HeaderSalesOrder::pluck('code', 'id')),
                                        DatePicker::make('payment_due')
                                            ->required(),
                                    ]),
                                TableRepeater::make('details')
                                    ->label('Product/Item')
                                    ->relationship('detail')
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Product')
                                            ->searchable()
                                            ->options(Product::pluck('name', 'id'))
                                            ->required(),
                                        TextInput::make('price')
                                            ->numeric()
                                            ->dehydrated(false)
                                            ->label('Price/unit')
                                            ->readOnly(),
                                        TextInput::make('qty')
                                            ->numeric(),
                                        TextInput::make('price_total')
                                            ->numeric()
                                            ->readOnly()
                                    ])
                                    ->reorderable()
                                    ->addActionLabel('Add')
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
                                            ->default(0),
                                        TextInput::make('total_amount')
                                            ->numeric()
                                            ->required()
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
                                        TextInput::make('total_weight')->numeric(),
                                        TextInput::make('shipment_price')->numeric()
                                            ->label('Shipment price (Rp.)')
                                    ])
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
