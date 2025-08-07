<?php

namespace App\Filament\Pages\FinanceSummary\Widgets;

use App\Models\Invoice;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TopCustomersTable extends BaseWidget
{
    protected static ?string $heading = 'Top Customers';
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        $query = Invoice::select('customer_id')
            ->selectRaw('SUM(total_amount) as total_sales')
            ->selectRaw('COUNT(*) as total_orders')
            ->with('customer:id,name')
            ->groupBy('customer_id')
            ->orderBy('total_orders', 'desc')
            ->limit(5);

            return $query;
    }

    public function getTableRecordKey(Model $record): string
    {
        return uniqid();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('customer.name')
                ->label('Customer')
                ->searchable()
                ->sortable(),
            TextColumn::make('total_orders')
                ->label('Orders')
                ->numeric()
                ->sortable(),
            TextColumn::make('total_sales')
                ->label('Total Sales')
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
