<?php

namespace App\Filament\Resources;

use App\Enum\Accounting\JournalSource;
use App\Enum\Accounting\JournalStatus;
use App\Filament\Resources\JournalEntryResource\Pages;
use App\Filament\Resources\JournalEntryResource\RelationManagers;
use App\Models\AccountingPeriods;
use App\Models\ChartOfAccount;
use App\Models\HeaderPurchaseOrder;
use App\Models\HeaderSalesOrder;
use App\Models\JournalEntry;
use App\Models\Payroll;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Throwable;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $navigationGroup = 'Accounting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        DatePicker::make('date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),
                        Select::make('status')
                            ->required()
                            ->default(0)
                            ->options([
                                0 => 'Unposted',
                                1 => 'Posted',
                            ]),
                        Select::make('source')
                            ->options(JournalSource::labels())
                            ->reactive()
                            ->label('Sumber'),
                        Select::make('source_id')
                            ->label('Referensi')
                            ->options(function (Get $get) {
                                $source = $get('source');

                                if ($source == JournalSource::PO->value) {
                                    return HeaderPurchaseOrder::pluck('code', 'id');
                                } else if ($source == JournalSource::SALES->value) {
                                    return HeaderSalesOrder::pluck('code', 'id');
                                } else if ($source == JournalSource::PAYROLL->value) {
                                    return Payroll::get()->mapWithKeys(function ($payroll) {
                                        return [
                                            $payroll->id => "{$payroll->month} {$payroll->year}",
                                        ];
                                    });
                                }

                                return [];
                            }),
                        TextInput::make('ref')
                            ->label('Nomor Referensi')
                            ->default(0),
                        Select::make('accounting_periods_id')
                            ->label('Periode')
                            ->required()
                            ->options(AccountingPeriods::pluck('name', 'id'))
                    ]),
                TableRepeater::make('details')
                    ->label(false)
                    ->relationship('details')
                    ->columnSpanFull()
                    ->cloneable()
                    ->reorderable()
                    ->schema([
                        Select::make('chart_of_account_id')
                            // ->options(ChartOfAccount::pluck('name', 'id'))
                            ->options(function () {
                                return ChartOfAccount::get()->mapWithKeys(function ($coa) {
                                    return [
                                        $coa->id => "{$coa->code} - {$coa->name}",
                                    ];
                                });
                            })
                            ->required()
                            ->label('COA')
                            ->searchable(['code', 'name']),
                        TextInput::make('description')
                            ->label('Keterangan'),
                        TextInput::make('debit')
                            ->default(0)
                            ->prefix('Rp. ')
                            ->label('Debit')
                            ->numeric(),
                        TextInput::make('kredit')
                            ->prefix('Rp. ')
                            ->default(0)
                            ->label('Kredit')
                            ->numeric(),
                    ]),
                Grid::make()
                    ->schema([
                        Section::make()
                            ->columnSpan(1)
                            ->schema([
                                Textarea::make('description')
                                    ->rows(3)
                                    ->label('Catatan tambahan')
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ref')
                    ->label('Nomor Referensi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('source')
                    ->label('Sumber')
                    ->badge()
                    ->formatStateUsing(fn($state) => JournalSource::tryFrom($state)?->label() ?? '-')
                    ->color('primary'),
                TextColumn::make('source_id')
                    ->label('Referensi')
                    ->formatStateUsing(function ($state, $record) {
                        $source = $record->source;

                        if ($source == JournalSource::PO->value) {
                            return HeaderPurchaseOrder::find($state)->code;
                        } else if ($source == JournalSource::SALES->value) {
                            return HeaderSalesOrder::find($state)->code;
                        } else if ($source == JournalSource::PAYROLL->value) {
                            $payroll = Payroll::find($state);
                            return $payroll->month . ' ' . $payroll->year;
                        }

                        return '-';
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => JournalStatus::tryFrom($state)?->label() ?? '-')
                    ->color(fn($state) => JournalStatus::tryFrom($state)?->color() ?? 'grey'),
                TextColumn::make('periods.name')
                    ->label('Periode')
                    ->searchable()
                    ->sortable()
            ])
            ->filters([
                SelectFilter::make('source')
                    ->options(JournalSource::labels())
                    ->label('Sumber'),
                SelectFilter::make('status')
                    ->options(JournalStatus::labels())
                    ->default(JournalStatus::UNPOSTED->value),
                SelectFilter::make('accounting_periods_id')
                    ->options(AccountingPeriods::pluck('name', 'id'))
                    ->label('Periode')
                    ->default(AccountingPeriods::open()->first()?->id)
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->status == JournalStatus::UNPOSTED->value)
                    ->color('warning'),
                Action::make('post')
                    ->label('Posting')
                    ->color('primary')
                    ->button()
                    ->requiresConfirmation()
                    ->action(fn($record) => self::setStatus($record, 1))
                    ->visible(fn($record) => $record->status == JournalStatus::UNPOSTED->value),
                Action::make('unpost')
                    ->label('Cancle Posting')
                    ->color('warning')
                    ->button()
                    ->requiresConfirmation()
                    ->action(fn($record) => self::setStatus($record, 0))
                    ->visible(fn($record) => $record->status == JournalStatus::POSTED->value),
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
            'index' => Pages\ListJournalEntries::route('/'),
            'create' => Pages\CreateJournalEntry::route('/create'),
            'edit' => Pages\EditJournalEntry::route('/{record}/edit'),
        ];
    }

    public static function setStatus(JournalEntry $journal, $status)
    {
        DB::beginTransaction();
        try {
            $journal->status = $status;
            $journal->save();

            DB::commit();

            Notification::make()
                ->title('Berhasil memposting')
                ->success()
                ->send();
        } catch (Throwable $th) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal')
                ->body($th->getMessage())
                ->error()
                ->send();
        }
    }
}
