<?php

namespace App\Filament\Resources;

use App\Enum\Accounting\JournalSource;
use App\Enum\Accounting\JournalStatus;
use App\Enum\Accounting\JournalType;
use App\Filament\Resources\JournalEntryResource\Pages;
use App\Filament\Resources\JournalEntryResource\RelationManagers;
use App\Models\AccountingPeriods;
use App\Models\ChartOfAccount;
use App\Models\HeaderPurchaseOrder;
use App\Models\HeaderSalesOrder;
use App\Models\Invoice;
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
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                ->columns(2)
                ->schema([
                    Section::make()
                    ->columnSpan(1)
                        ->schema([
                            DatePicker::make('date')
                                ->label('Tanggal')
                                ->default(now())
                                ->required(),
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
                        ]),
                    Section::make()
                    ->columnSpan(1)
                        ->schema([
                            Select::make('status')
                                ->required()
                                ->default(0)
                                ->options([
                                    0 => 'Unposted',
                                    1 => 'Posted',
                                ]),
                            Select::make('accounting_periods_id')
                                ->label('Periode')
                                ->required()
                                ->options(AccountingPeriods::open()->pluck('name', 'id')),
                            Select::make('type')
                                ->options(JournalType::labels())
                                ->searchable()
                                ->required()
                                ->label('Tipe Jurnal')
                        ])
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
                TextColumn::make('type')
                    ->formatStateUsing(fn($state) => JournalType::tryFrom($state)?->labels() ?? '-')
                    ->label('Tipe')
                    ->searchable(),
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('source')
                    ->label('Sumber')
                    ->badge()
                    ->formatStateUsing(fn($state) => JournalSource::tryFrom($state)?->label() ?? '-')
                    ->default('-')
                    ->color('primary'),
                TextColumn::make('source_id')
                    ->label('Referensi')
                    ->default('-')
                    ->formatStateUsing(function ($state, $record) {
                        return match ($record->source) {
                            JournalSource::PO->value => optional(HeaderPurchaseOrder::find($state))->code ?? '-',
                            JournalSource::SALES->value => optional(Invoice::find($state))->inv_no ?? '-',
                            JournalSource::PAYROLL->value => function () use ($state) {
                                $payroll = Payroll::find($state);
                                return $payroll ? "{$payroll->month} {$payroll->year}" : '-';
                            },
                            default => '-',
                        };
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
