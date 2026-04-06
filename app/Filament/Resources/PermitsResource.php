<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermitsResource\Pages;
use App\Models\Permits;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PermitsResource extends Resource
{
    protected static ?string $model = Permits::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = 'Human Resource';
    protected static ?string $label = 'Izin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make()
                            ->columnSpan(1)
                            ->schema([
                                Select::make('lead_id')
                                    ->label('Lead')
                                    ->options(\App\Models\User::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                                Select::make('type')
                                    ->label('Jenis Izin')
                                    ->options(\App\Enum\HumanResource\PermitType::labels())
                                    ->required(),
                                DatePicker::make('date')
                                    ->label('Tanggal')
                                    ->default(now())
                                    ->displayFormat('d/m/Y')
                                    ->required(),
                            ]),
                        Section::make()
                            ->columnSpan(1)
                            ->schema([
                                Textarea::make('reason')
                                    ->label('Alasan')
                                    ->rows(3)
                                    ->required(),
                                FileUpload::make('attachment')
                                    ->label('Lampiran')
                                    ->required(),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Karyawan')->searchable()->sortable(),
                TextColumn::make('lead.name')->label('Lead')->searchable()->sortable(),
                TextColumn::make('type')
                    ->label('Jenis Izin')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? '-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->searchable()
                    ->date()
                    ->sortable(),
                IconColumn::make('status')
                    ->label('Status')
                    ->color(fn ($state) => $state?->color() ?? null)
                    ->icon(fn ($state) => $state?->icon() ?? null)
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(\App\Enum\ApprovalStatus::labels()),
                SelectFilter::make('type')
                    ->label('Jenis Izin')
                    ->options(\App\Enum\HumanResource\PermitType::labels()),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('approve')
                        ->label('Setujui')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->visible(fn (Permits $record) => $record->status !== \App\Enum\ApprovalStatus::APPROVED)
                        ->action(function (Permits $record) {
                            self::setStatus($record, \App\Enum\ApprovalStatus::APPROVED);
                        }),
                    Action::make('reject')
                        ->label('Tolak')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->visible(fn (Permits $record) => $record->status !== \App\Enum\ApprovalStatus::REJECTED)
                        ->action(function (Permits $record) {
                            self::setStatus($record, \App\Enum\ApprovalStatus::REJECTED);
                        }),
                ]),
            ], ActionsPosition::BeforeColumns)
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
            'index' => Pages\ListPermits::route('/'),
            'create' => Pages\CreatePermits::route('/create'),
            'edit' => Pages\EditPermits::route('/{record}/edit'),
        ];
    }

    public static function setStatus(Permits $record, \App\Enum\ApprovalStatus $status): void
    {
        $record->status = $status;
        $record->save();

        Notification::make()
            ->title("Izin {$status->label()}")
            ->success()
            ->send();
    }
}
