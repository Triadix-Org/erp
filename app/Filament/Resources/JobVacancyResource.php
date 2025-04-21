<?php

namespace App\Filament\Resources;

use App\Enum\Employee\Education;
use App\Filament\Resources\JobVacancyResource\Pages;
use App\Filament\Resources\JobVacancyResource\RelationManagers;
use App\Models\JobVacancy;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class JobVacancyResource extends Resource
{
    protected static ?string $model = JobVacancy::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Human Resource';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->columns(5)
                    ->schema([
                        Forms\Components\Section::make()
                            ->columnSpan(3)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Job Title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\MarkdownEditor::make('job_desc')
                                    ->toolbarButtons([
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'heading',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo',
                                    ])
                                    ->required(),
                                Forms\Components\MarkdownEditor::make('job_requirements')
                                    ->toolbarButtons([
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'heading',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('published_by')
                                    ->readOnly()
                                    ->default(Auth::user()->name)
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\DatePicker::make('post_date')
                                    ->default(now())
                                    ->required(),
                                Forms\Components\DatePicker::make('close_date'),
                                Forms\Components\TextInput::make('salary')
                                    ->prefix('Rp. ')
                                    ->numeric(),
                                Forms\Components\TextInput::make('dept_div')
                                    ->maxLength(255),
                                Forms\Components\Select::make('contract_type')
                                    ->options([
                                        1 => 'Internship',
                                        2 => 'Full time',
                                        3 => 'Part time',
                                        4 => 'Freelance',
                                        5 => 'Contract',
                                    ]),
                                Forms\Components\Select::make('working_type')
                                    ->options([
                                        1 => 'WFO',
                                        2 => 'Remote',
                                    ]),
                                Select::make('minimum_education')
                                    ->options(Education::labels()),
                                Forms\Components\TextInput::make('years_of_experience')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('location')
                                    ->maxLength(255),
                            ])
                            ->columnSpan(2)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('post_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('close_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('salary')
                    ->prefix('Rp. ')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dept_div')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contract_type_str')
                    ->sortable()
                    ->label('Contract Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('working_type_str')
                    ->numeric()
                    ->label('Working Type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_education')
                    ->searchable(),
                Tables\Columns\TextColumn::make('years_of_experience')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('status'),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->color('warning'),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->tooltip('Actions'),
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
            'index' => Pages\ListJobVacancies::route('/'),
            'create' => Pages\CreateJobVacancy::route('/create'),
            'edit' => Pages\EditJobVacancy::route('/{record}/edit'),
        ];
    }
}
