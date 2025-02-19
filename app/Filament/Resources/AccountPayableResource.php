<?php

namespace App\Filament\Resources;

use App\Enum\OutcomeType;
use App\Enum\PaymentStatus;
use App\Filament\Resources\AccountPayableResource\Pages;
use App\Filament\Resources\AccountPayableResource\RelationManagers;
use App\Models\AccountsPayable;
use App\Models\HeaderPurchaseOrder;
use App\Models\Outcome;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class AccountPayableResource extends Resource
{
    protected static ?string $model = AccountsPayable::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('header_purchase_order_id')
                                    ->relationship(name: 'purchaseOrder', titleAttribute: 'code'),
                                Select::make('supplier_id')
                                    ->relationship(name: 'supplier', titleAttribute: 'name'),
                                DatePicker::make('date'),
                                DatePicker::make('due_date'),
                                TextInput::make('amount'),
                                Select::make('status')
                                    ->options(PaymentStatus::class),
                                DatePicker::make('payment_date'),
                                FileUpload::make('attach')
                                    ->label('Proof of Payment')
                                    ->openable()
                                    ->downloadable()
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('purchaseOrder.code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->sortable(),
                TextColumn::make('amount')
                    ->sortable()
                    ->money('idr'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => PaymentStatus::tryFrom($state)?->label() ?? '-')
                    ->color(fn($state) => PaymentStatus::tryFrom($state)?->color() ?? 'gray'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options(PaymentStatus::labels())
                    ->default([
                        PaymentStatus::UNPAID->value, // Nilai default pertama
                        PaymentStatus::OVER->value, // Nilai default kedua
                    ]),
                Filter::make('due_date')
                    ->form([
                        Section::make('Due Date Range')
                            ->schema([
                                DatePicker::make('date_from')->label('Due Date From'),
                                DatePicker::make('date_until')->label('Due Date Until'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('due_date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('due_date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                ViewAction::make()
                    ->color('info'),
                Action::make('payment_info')
                    ->label('Pay')
                    ->modalHeading('Pay Accounts Payable')
                    ->icon('heroicon-s-credit-card')
                    ->color('primary')
                    ->button()
                    ->visible(fn($record) => $record->status == 0)
                    ->form([
                        Section::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('supplier.code')
                                    ->label('Supplier Code')
                                    ->default(function ($record) {
                                        return $record->supplier->code;
                                    })
                                    ->disabled(),
                                TextInput::make('supplier.name')
                                    ->label('Name')
                                    ->default(function ($record) {
                                        return $record->supplier->name;
                                    })
                                    ->disabled(),
                                TextInput::make('supplier.pic')
                                    ->label('PIC')
                                    ->default(function ($record) {
                                        return $record->supplier->pic;
                                    })
                                    ->disabled(),
                                TextInput::make('supplier.handphone')
                                    ->label('Phone')
                                    ->default(function ($record) {
                                        return $record->supplier->handphone;
                                    })
                                    ->disabled(),
                                TextInput::make('supplier.payment_first')
                                    ->label('First Payment')
                                    ->default(function ($record) {
                                        return $record->supplier->payment_first;
                                    })
                                    ->disabled(),
                                TextInput::make('supplier.val_payment_first')
                                    ->label('Bank Number')
                                    ->default(function ($record) {
                                        return $record->supplier->val_payment_first;
                                    })
                                    ->disabled(),
                                TextInput::make('supplier.payment_second')
                                    ->label('Second Payment')
                                    ->default(function ($record) {
                                        return $record->supplier->payment_second;
                                    })
                                    ->disabled(),
                                TextInput::make('supplier.val_payment_second')
                                    ->label('Bank Number')
                                    ->default(function ($record) {
                                        return $record->supplier->val_payment_second;
                                    })
                                    ->disabled(),
                                FileUpload::make('attach')
                                    ->label('Proof of Payment')
                                    ->directory('accounts-payable')
                                    ->image()
                                    ->openable()
                                    ->downloadable()
                                    ->required()
                            ]),
                    ])
                    ->action(function (array $data, AccountsPayable $record): void {
                        $record->attach = $data['attach'];
                        $record->payment_date = now();
                        $record->status = 1;
                        $record->save();

                        $supplier = Supplier::find($record->supplier_id);

                        DB::transaction(function () use ($record, $supplier) {
                            $purchaseOrder = HeaderPurchaseOrder::find($record->header_purchase_order_id);
                            if ($purchaseOrder) {
                                $purchaseOrder->payment_status = 1;
                                $purchaseOrder->save();
                            }

                            // Add to outcome data
                            $outcome = new Outcome();
                            $outcome->date      = now();
                            $outcome->amount    = $record->amount;
                            $outcome->type      = OutcomeType::OTHER;
                            $outcome->pay_to    = $supplier->name;
                            $outcome->status    = 1;
                            $outcome->save();
                        });

                        Notification::make()
                            ->success()
                            ->title('Saved Successfully!')
                            ->send();
                    })
            ], position: ActionsPosition::BeforeColumns)
            ->defaultSort('date', 'asc');
    }

    protected function applyDefaultSortingToTableQuery(Builder $query): Builder
    {
        return $query
            ->orderBy('status', 'asc')
            ->orderBy('due_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAccountPayables::route('/'),
        ];
    }
}
