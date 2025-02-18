<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivisionResource\Pages;
use App\Filament\Resources\DivisionResource\RelationManagers;
use App\Models\Division;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Division Name')
                            ->required(),
                        Select::make('director')
                            ->required()
                            ->searchable()
                            ->options(User::all()->pluck('name', 'id')),
                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Division Name')
                    ->searchable(),
                TextColumn::make('directora.name')
                    ->label('Director')
                    ->searchable(),
                TextColumn::make('description'),
                ToggleColumn::make('status')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageDivisions::route('/'),
        ];
    }
}
