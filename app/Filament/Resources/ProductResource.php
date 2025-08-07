<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\Widgets\Product as ProductWidget;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $label = 'Produk';
    protected static ?string $pluralLabel = 'Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('warehouse_id')
                            ->label('Warehouse')
                            ->options(Warehouse::pluck('name', 'id'))
                            ->searchable(),
                        TextInput::make('desc')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp.'),
                        TextInput::make('unit')
                            ->required()
                            ->maxLength(255),
                        Select::make('category_code')
                            ->label('Category')
                            ->options(function () {
                                return ProductCategory::pluck('name', 'code');
                            })
                            ->required(),
                        Select::make('status')
                            ->required()
                            ->options([
                                '1' => 'Active',
                                '0' => 'Non Active',
                            ]),
                        TextInput::make('stock')
                            ->required()
                            ->numeric(),
                        TextInput::make('weight')
                            ->label('Weight (Kg)')
                            ->required()
                            ->numeric(),
                        TextInput::make('dimension')
                            ->label('Dimension (cm)')
                            ->required(),
                        FileUpload::make('thumbnail')
                            ->directory('product')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('warehouse.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('desc')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Price (Rp)')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->searchable(),
                ImageColumn::make('thumbnail'),
                ToggleColumn::make('status'),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                SelectFilter::make('warehouse')
                    ->relationship('warehouse', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make(),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            ProductWidget::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}
