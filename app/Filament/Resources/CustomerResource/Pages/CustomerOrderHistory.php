<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\Filter;
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
    public $totalOrders;
    public $totalAmount;

    public function mount($record): void
    {
        $this->record = $record;
        $custData = Customer::find($record);
        $this->custName = $custData->name;
        static::$title = 'Order History ' . $this->custName;

        self::getSummary();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                TextColumn::make('inv_no')
                    ->searchable(),
                TextColumn::make('date')
                    ->searchable()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->locale('id')->translatedFormat('d F Y h:i')),
                TextColumn::make('total_tax')
                    ->label('Tax')
                    ->money('idr'),
                TextColumn::make('shipment_price')
                    ->money('idr'),
                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('idr')
            ])
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('date_from'),
                        DatePicker::make('date_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('info'),
                    ActionsAction::make('pdf')
                        ->label('Print PDF')
                        ->color('primary')
                        ->icon('heroicon-s-document')
                ])
            ], position: ActionsPosition::BeforeColumns);
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

    protected function getSummary()
    {
        $this->totalOrders = Invoice::where('customer_id', $this->record)->count();

        $amount = Invoice::where('customer_id', $this->record)->sum('total_amount');
        $this->totalAmount = number_format($amount, 0, ',', '.');
    }
}
