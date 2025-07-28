<?php

namespace App\Filament\Resources;

use App\Enum\Employee\ApplyStatus;
use App\Filament\Resources\JobApplicationResource\Pages;
use App\Filament\Resources\JobApplicationResource\RelationManagers;
use App\Models\JobApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Throwable;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Human Resource';
    protected static ?string $label = 'Lamaran Masuk';
    protected static ?string $pluralLabel = 'Lamaran Masuk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('job_vacancy_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date_of_birth')
                    ->required(),
                Forms\Components\TextInput::make('education')
                    ->numeric(),
                Forms\Components\TextInput::make('years_of_experience')
                    ->numeric(),
                Forms\Components\FileUpload::make('resume')
                    ->required()
                    ->directory('resume')
                    ->openable(),
                Forms\Components\FileUpload::make('application_letter')
                    ->required()
                    ->directory('application_letter')
                    ->openable(),
                Forms\Components\FileUpload::make('certificate')
                    ->required()
                    ->directory('certificate')
                    ->openable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vacancy.title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('education')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('years_of_experience')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => ApplyStatus::tryFrom($state)?->label() ?? '-')
                    ->color(fn($state) => ApplyStatus::tryFrom($state)?->color() ?? 'gray'),
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
                SelectFilter::make('status')
                    ->options(ApplyStatus::labels())
            ])
            ->actions([
                ActionGroup::make(
                    array_merge(
                        [
                            ViewAction::make()->color('info'),
                        ],
                        array_map(
                            fn($status) =>
                            Action::make('setStatus' . $status->value)
                                ->label('Set status to ' . $status->label())
                                ->icon('heroicon-s-check-circle')
                                ->color($status->color())
                                ->action(fn($record) => self::setStatus($record->getKey(), $status->value)),
                            ApplyStatus::cases()
                        )
                    )
                )
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
            'index' => Pages\ListJobApplications::route('/'),
            'create' => Pages\CreateJobApplication::route('/create'),
        ];
    }

    public static function setStatus($id, $status)
    {
        try {
            DB::beginTransaction();

            $record = JobApplication::find($id);
            if ($record) {
                $record->status = $status;
                $record->save();
            }

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
