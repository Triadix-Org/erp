<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\StockOpname as ModelsStockOpname;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class StockOpname extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ProductResource::class;

    protected static string $view = 'filament.resources.product-resource.pages.stock-opname';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createSO')
                ->label('Create Stock Opname')
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(ModelsStockOpname::query())
            ->columns([
                TextColumn::make('date')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
