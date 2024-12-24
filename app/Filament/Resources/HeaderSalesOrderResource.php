<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeaderSalesOrderResource\Pages;
use App\Filament\Resources\HeaderSalesOrderResource\RelationManagers;
use App\Models\Customer;
use App\Models\HeaderSalesOrder;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class HeaderSalesOrderResource extends Resource
{
    protected static ?string $model = HeaderSalesOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?string $navigationGroup = 'Sales & Marketing';
    protected static ?string $label = 'Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('sales_date')
                    ->default(Carbon::now())
                    ->required(),
                Forms\Components\DatePicker::make('due_date'),
                TextInput::make('sales_by')
                    ->default(Auth::user()->email)
                    ->required()
                    ->readOnly(),
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer')
                    ->label('Customer/Buyer')
                    ->options(Customer::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('note')
                    ->cols(3),
                Forms\Components\Textarea::make('payment_terms')
                    ->cols(3),
                TableRepeater::make('details')
                    ->label('Product/Item')
                    ->relationship('detail')
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->searchable()
                            ->options(Product::pluck('name', 'id'))
                            ->required(),

                        TextInput::make('qty')
                            ->numeric(),
                    ])
                    ->reorderable()
                    ->addActionLabel('Add')
                    ->columnSpan('full'),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp.')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sales_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->prefix('Rp.')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('note')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_terms')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => fn($state): bool => $state == 3,
                        'danger' => fn($state): bool => $state == 2,
                        'warning' => fn($state): bool => $state == 1,
                        'info' => fn($state): bool => $state == 0,
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        0 => 'Open',
                        1 => 'Process',
                        2 => 'Decline',
                        3 => 'Done',
                        default => 'Unknown',
                    }),
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
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('setStatusProcess')
                        ->label('Process Order')
                        ->icon('heroicon-o-check')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Confirmation')
                        ->modalDescription('Are you sure you want to change status?')
                        ->action(function ($record) {
                            $orderNumber = $record->code;
                            self::setStatus($orderNumber, 1);
                        }),
                    Action::make('setStatusDone')
                        ->label('Order Done')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Confirmation')
                        ->modalDescription('Are you sure you want to change status?')
                        ->action(function ($record) {
                            $orderNumber = $record->code;
                            self::setStatus($orderNumber, 3);
                        }),
                    Action::make('setStatusDecline')
                        ->label('Order Decline')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Confirmation')
                        ->modalDescription('Are you sure you want to change status?')
                        ->action(function ($record) {
                            $orderNumber = $record->code;
                            self::setStatus($orderNumber, 2);
                        }),
                ]),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHeaderSalesOrders::route('/'),
        ];
    }

    public static function setStatus($orderNumber, $status)
    {
        try {
            DB::beginTransaction();

            $record = HeaderSalesOrder::where('code', $orderNumber)->first();
            $record->status = $status;
            $record->save();

            DB::commit();
            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();
        } catch (Throwable $th) {
            DB::rollBack();
            Notification::make()
                ->title('Opps.. Something went wrong!')
                ->body($th->getMessage())
                ->danger()
                ->send();
        }
    }
}
