<?php

namespace App\Filament\Pages\FinanceSummary\Widgets;

use App\Models\Invoice;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestSalesTable extends BaseWidget
{
    protected static ?string $heading = 'Penjualan Terakhir';
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Invoice::with('customer')
            ->orderBy('date', 'desc')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('inv_no')
                ->label('Invoice')
                ->searchable()
                ->sortable(),
            TextColumn::make('date')
                ->label('Date')
                ->date('d/m/Y')
                ->sortable(),
            TextColumn::make('customer.name')
                ->label('Customer')
                ->searchable()
                ->sortable(),
            TextColumn::make('total_amount')
                ->label('Amount')
                ->formatStateUsing(fn($state) => format_currency($state))
                ->sortable(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->paginated(false);
    }
}
