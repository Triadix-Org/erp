<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeaderRequestOrderResource\Pages;
use App\Filament\Resources\HeaderRequestOrderResource\RelationManagers;
use App\Models\HeaderRequestOrder;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class HeaderRequestOrderResource extends Resource
{
    protected static ?string $model = HeaderRequestOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Material Request';
    protected static ?string $navigationGroup = 'Production';
    protected static ?string $label = 'Material Request';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->label('Request Date')
                    ->default(Carbon::now())
                    ->readOnly()
                    ->required(),
                TextInput::make('req_by')
                    ->required()
                    ->default(fn() => auth()->user()->email)
                    ->readOnly(),
                DatePicker::make('due_date')
                    ->required(),
                TextInput::make('note')
                    ->maxLength(255),
                Section::make('Details')
                    ->schema([
                        TableRepeater::make('details')
                            ->relationship('detail')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::isActive()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),

                                TextInput::make('qty')
                                    ->numeric(),
                            ])
                            ->reorderable()
                            ->columnSpan('full'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('req_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('app_manager')
                    ->label('Approval')
                    ->badge()
                    ->colors([
                        'success' => fn($state): bool => $state == 1,
                        'warning' => fn($state): bool => $state == 0,
                    ])
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Approved' : 'Open'),
                Tables\Columns\TextColumn::make('approved_by')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('status'),
                Tables\Columns\TextColumn::make('proses')
                    ->badge()
                    ->colors([
                        'success' => fn($state): bool => $state == 1,
                        'warning' => fn($state): bool => $state == 0,
                    ])
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Diproses' : 'Belum Diproses'),
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
                    DeleteAction::make(),
                    Action::make('approveRequest')
                        ->label('Approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Confirmation')
                        ->modalDescription('Are you sure you want to approve?')
                        ->action(function ($record) {
                            $reqNumber = $record->code;
                            self::approveRequest($reqNumber);
                        })
                        ->visible(fn($record) => $record->app_manager == 0 &&
                            (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Production Manager'))),
                    Action::make('cancelApproveRequest')
                        ->label('Cancel Approve')
                        ->icon('heroicon-o-x-mark')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Confirmation')
                        ->modalDescription('Are you sure you want to cancel approve?')
                        ->action(function ($record) {
                            $reqNumber = $record->code;
                            self::cancelApproveRequest($reqNumber);
                        })
                        ->visible(fn($record) => $record->app_manager == 1 && (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Production Manager'))),
                    Action::make('print')
                        ->label('Print PDF')
                        ->color('info')
                        ->icon('heroicon-o-printer')
                        ->url(fn($record) => env('APP_URL') . '/sales/material-request/pdf/' . $record->code)
                        ->openUrlInNewTab()
                ])
            ], position: ActionsPosition::BeforeColumns)
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
            'index' => Pages\ListHeaderRequestOrders::route('/'),
            'create' => Pages\CreateHeaderRequestOrder::route('/create'),
            'edit' => Pages\EditHeaderRequestOrder::route('/{record}/edit'),
        ];
    }

    public static function approveRequest($reqNumber)
    {
        try {
            DB::beginTransaction();

            $record = HeaderRequestOrder::where('code', $reqNumber)->first();
            $record->app_manager = 1;
            $record->approved_by = Auth::user()->email;
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
                ->danger()
                ->send();
        }
    }

    public static function cancelApproveRequest($reqNumber)
    {
        try {
            DB::beginTransaction();

            $record = HeaderRequestOrder::where('code', $reqNumber)->first();
            $record->app_manager = 0;
            $record->approved_by = null;
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
                ->danger()
                ->send();
        }
    }
}
