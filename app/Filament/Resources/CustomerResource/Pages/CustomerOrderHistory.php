<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerOrderHistory extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $resource = CustomerResource::class;

    protected static string $view = 'filament.resources.customer-resource.pages.customer-order-history';
    protected static ?string $title = null;

    public $record; // Untuk menyimpan data customer
    public $custName;

    public function mount($record): void
    {
        $this->record = $record;
        $custData = Customer::find($record);
        $this->custName = $custData->name;
        static::$title = 'Order History ' . $this->custName;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                TextColumn::make('inv_no')
                    ->searchable(),
                TextColumn::make('date')
                    ->searchable(),
            ])
            ->filters([
                //
            ]);
    }

    protected function getFilteredQuery(): Builder
    {
        return Invoice::where('customer_id', $this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Data')
        ];
    }
}
