<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Filament\Resources\QuotationResource\RelationManagers;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Sales & Marketing';

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
                        TableRepeater::make('Details')
                            ->label('Product/Item')
                            ->relationship('details')
                            ->addActionLabel('Add Product')
                            ->reorderable()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->searchable()
                                    ->options(Product::pluck('name', 'id'))
                                    ->required(),
                                TextInput::make('unit_price')
                                    ->numeric()
                                    ->label('Price/unit')
                                    ->reactive()
                                    ->readOnly(),
                                TextInput::make('qty')
                                    ->readOnly()
                                    ->numeric(),
                                TextInput::make('total_price')
                                    ->numeric()
                                    ->readOnly()
                                    ->reactive()
                            ])
                            ->colStyles(function () {
                                return [
                                    'product_id' => 'width: 45%; font-size:12px;',
                                    'unit' => 'width: 15%; font-size:12px;',
                                    'qty' => 'width: 15%; font-size:12px;',
                                    'total_price' => 'width: 20%; font-size:12px;',
                                ];
                            })
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_id')
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
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }
}
