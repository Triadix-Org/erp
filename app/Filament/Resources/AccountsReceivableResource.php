<?php

namespace App\Filament\Resources;

use App\Enum\IncomeType;
use App\Enum\PaymentStatus;
use App\Filament\Resources\AccountsReceivableResource\Pages;
use App\Filament\Resources\AccountsReceivableResource\RelationManagers;
use App\Models\AccountsReceivable;
use App\Models\Customer;
use App\Models\Income;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class AccountsReceivableResource extends Resource
{
    protected static ?string $model = AccountsReceivable::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $label = 'Piutang';
    protected static ?string $pluralLabel = 'Piutang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('invoice_id')
                    ->required()
                    ->numeric(),
                TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('date')
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('attach')
                    ->maxLength(255),
                DatePicker::make('payment_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.inv_no')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => PaymentStatus::tryFrom($state)?->label() ?? '-')
                    ->color(fn($state) => PaymentStatus::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
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
                //
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('setPaid')
                        ->color('info')
                        ->label('Set to Paid')
                        ->modalHeading('Set Status to Paid')
                        ->icon('heroicon-s-check-circle')
                        ->visible(fn($record) => $record->status == 0)
                        ->form([
                            Section::make()
                                ->schema([
                                    FileUpload::make('attach')
                                        ->label('Proof of Payment')
                                        ->directory('accounts-receivable')
                                ])
                        ])
                        ->action(function (array $data, AccountsReceivable $record): void {
                            $record->attach = $data['attach'];
                            $record->payment_date = now();
                            $record->status = 1;
                            $record->save();

                            $cust = Customer::find($record->customer_id);

                            DB::transaction(function () use ($record, $cust) {
                                $invoice = Invoice::find($record->invoice_id);
                                if ($invoice) {
                                    $invoice->payment_status = 1;
                                    $invoice->save();
                                }

                                // Add to outcome data
                                $outcome = new Income();
                                $outcome->date      = now();
                                $outcome->amount    = $record->amount;
                                $outcome->type      = IncomeType::SALES;
                                $outcome->from      = $cust->name;
                                $outcome->status    = 1;
                                $outcome->save();
                            });

                            Notification::make()
                                ->success()
                                ->title('Saved Successfully!')
                                ->send();
                        }),
                    Tables\Actions\EditAction::make()->color('warning'),
                    Tables\Actions\DeleteAction::make(),
                ])
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAccountsReceivables::route('/'),
        ];
    }
}
