<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeaderMaterialReceivedResource\Pages;
use App\Models\HeaderMaterialReceived;
use App\Models\HeaderPurchaseOrder;
use App\Models\Supplier;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
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
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class HeaderMaterialReceivedResource extends Resource
{
    protected static ?string $model = HeaderMaterialReceived::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $pluralLabel = 'Tanda Terima Barang';
    protected static ?string $navigationGroup = 'Purchasing';
    protected static ?string $label = 'Tanda Terima Barang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->columns(5)
                    ->schema([
                        Fieldset::make('MRN Detail')
                            ->columnSpan(3)
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->default(Carbon::now())
                                    ->required(),
                                Forms\Components\DatePicker::make('delivery_date'),
                                Select::make('supplier_id')
                                    ->label('Vendor/Supplier')
                                    ->required()
                                    ->options(Supplier::where('status', 1)
                                        ->pluck('name', 'id'))
                                    ->searchable(),
                                Select::make('header_purchase_order_id')
                                    ->label('PO Number')
                                    ->required()
                                    ->options(HeaderPurchaseOrder::where('status', 1)
                                        ->pluck('code', 'id'))
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $headerId = $get('header_purchase_order_id');

                                        if ($headerId) {
                                            // $details = DetailPurchaseOrder::with('product:id,price')->where('header_purchase_order_id', $headerId)->get();
                                            $details = HeaderPurchaseOrder::with('detail:id,header_purchase_order_id,product_id,qty')->find($headerId);
                                            // dd($details);

                                            $set('details', $details->detail->map(fn($detail) => [
                                                'product_id' => $detail->product_id,
                                                'qty' => $detail->qty
                                            ])->toArray());
                                            $set('total_amount', $details->total_amount);
                                        } else {
                                            $set('details', []);
                                        }
                                    }),
                                Forms\Components\TextInput::make('received_by')
                                    ->readOnly()
                                    ->default(Auth::user()->email)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('total_items')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('total_amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp.')
                                    ->default(0),
                                Forms\Components\Textarea::make('received_condition')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('comment')
                                    ->maxLength(255),
                            ]),
                        Fieldset::make('Item/Goods')
                            ->columnSpan(2)
                            ->schema([
                                TableRepeater::make('details')
                                    ->label(false)
                                    ->relationship('detail')
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Product')
                                            ->relationship(
                                                name: 'product',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn(Builder $query) => $query->where('status', 1),
                                            )
                                            ->required(),

                                        TextInput::make('qty')
                                            ->numeric()
                                            ->reactive(),

                                    ])
                                    ->reorderable()
                                    ->columnSpan('full'),
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
                Tables\Columns\TextColumn::make('po.code')
                    ->label('PO Number')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('received_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_items')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qc_by')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ViewAction::make()
                        ->modalWidth('6xl'),
                    Tables\Actions\EditAction::make()->color('warning'),
                    DeleteAction::make(),
                    Action::make('print')
                        ->label('Print PDF')
                        ->color('info')
                        ->icon('heroicon-o-printer')
                        ->url(fn($record) => env('APP_URL') . '/purchasing/material-received-note/pdf/' . $record->code)
                        ->openUrlInNewTab(),
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
            'index' => Pages\ListHeaderMaterialReceiveds::route('/'),
            'create' => Pages\CreateHeaderMaterialReceived::route('/create'),
            'edit' => Pages\EditHeaderMaterialReceived::route('/{record}/edit'),
        ];
    }
}
