<?php

namespace App\Filament\Resources;

use App\Enum\Employee\Gender;
use App\Enum\Employee\MarriageStatus;
use App\Enum\Employee\Religion;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Division;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Human Resource';
    protected static ?string $label = 'Data Pegawai';
    protected static ?string $pluralLabel = 'Data Pegawai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('tabs')
                    ->tabs([
                        Tab::make('profile')
                            ->label('Personal Info')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 1,
                                            'md' => 2,
                                            'lg' => 2,
                                            'xl' => 2,
                                            '2xl' => 2,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('nip')
                                                    ->readOnly()
                                                    ->dehydrated(false)
                                                    ->placeholder('Auto'),
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nama Lengkap')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('nik')
                                                    ->label('NIK')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('place_of_birth')
                                                    ->label('Tempat Lahir')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\DatePicker::make('date_of_birth')
                                                    ->label('Tanggal Lahir')
                                                    ->required(),
                                                Select::make('gender')
                                                    ->label('Jenis Kelamin')
                                                    ->options(Gender::labels())
                                                    ->required(),
                                                Select::make('religion')
                                                    ->label('Agama')
                                                    ->options(Religion::labels())
                                                    ->required(),
                                                Select::make('marriage_status')
                                                    ->label('Status Pernikahan')
                                                    ->options(MarriageStatus::labels())
                                                    ->required(),
                                                Forms\Components\TextInput::make('address')
                                                    ->label('Alamat')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('phone')
                                                    ->label('No. Telp')
                                                    ->tel()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('email')
                                                    ->email()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('emergency_phone')
                                                    ->label('No. Telp Darurat')
                                                    ->tel()
                                                    ->maxLength(255),
                                                Select::make('user_id')
                                                    ->required()
                                                    ->label('Akun')
                                                    ->options(User::all()->pluck('name', 'id'))
                                                    ->searchable(),
                                                FileUpload::make('photo')
                                                    ->directory('employee/photo')
                                            ])
                                    ])
                            ]),
                        Tab::make('personnel')
                            ->label('Employment Info')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 1,
                                            'md' => 2,
                                            'lg' => 2,
                                            'xl' => 2,
                                            '2xl' => 2,
                                        ])
                                            ->relationship('personnel')
                                            ->schema([
                                                TextInput::make('nip')
                                                    ->label('NIP')
                                                    ->readOnly()
                                                    ->dehydrated(false)
                                                    ->placeholder('Auto'),
                                                TextInput::make('position')
                                                    ->label('Posisi')
                                                    ->required()
                                                    ->maxLength(255),
                                                Select::make('div')
                                                    ->label('Divisi')
                                                    ->required()
                                                    ->options(Division::all()->pluck('name', 'id'))
                                                    ->searchable(),
                                                Select::make('dept')
                                                    ->required()
                                                    ->label('Departemen')
                                                    ->options(Department::all()->pluck('name', 'id'))
                                                    ->searchable(),
                                                TextInput::make('position_level')
                                                    ->label('Level Posisi'),
                                                Select::make('employment_status')
                                                    ->label('Status Kepegawaian')
                                                    ->required()
                                                    ->options([
                                                        0 => 'Internship',
                                                        1 => 'PKWT',
                                                        2 => 'PKWTT',
                                                    ]),
                                                DatePicker::make('start_working')
                                                    ->label('Mulai Bekerja')
                                                    ->required(),
                                                DatePicker::make('end_working')
                                                    ->label('Selesai Bekerja')
                                                    ->required(),
                                                TextInput::make('leave_quota')
                                                    ->label('Kuota Cuti (hari)')
                                                    ->numeric()
                                                    ->required(),
                                                Fieldset::make('Bank Account')
                                                    ->label('Rekening Bank')
                                                    ->schema([
                                                        TextInput::make('bank')
                                                            ->label('Nama Bank'),
                                                        TextInput::make('bank_number')
                                                            ->label('Nomor Rekening'),
                                                        TextInput::make('bank_account_name')
                                                            ->label('Nama Pemilik Rekening'),
                                                    ]),
                                            ])
                                    ])
                            ]),
                        Tab::make('Sallary')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Grid::make()
                                            ->relationship('personnel')
                                            ->schema([
                                                TextInput::make('sallary')
                                                    ->label('Sallary (Rp.)')
                                                    ->numeric()
                                                    ->required()
                                                    ->columnSpanFull()
                                            ])
                                    ])
                            ])
                    ])
                    ->contained(false)
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('place_of_birth')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->formatStateUsing(fn($state) => Gender::tryFrom($state)?->label() ?? '-')
                    ->badge()
                    ->colors(['primary'])
                    ->searchable(),
                TextColumn::make('religion')
                    ->label('Religion')
                    ->formatStateUsing(fn($state) => Religion::tryFrom($state)?->label() ?? '-')
                    ->badge()
                    ->colors(['info'])
                    ->searchable(),
                TextColumn::make('marriage_status')
                    ->label('Marriage Status')
                    ->formatStateUsing(fn($state) => MarriageStatus::tryFrom($state)?->label() ?? '-')
                    ->badge()
                    ->colors(['warning'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('emergency_phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('photo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->color('info'),
                    Tables\Actions\EditAction::make()->color('warning'),
                    Tables\Actions\DeleteAction::make(),
                ]),
                Tables\Actions\Action::make('id')
                    ->label(false)
                    ->icon('heroicon-s-identification')
                    ->iconSize('lg')
                    ->tooltip('Print ID Card')
                    ->url(fn($record) => env('APP_URL') . '/human-resource/id-card/' . $record->nip)
                    ->openUrlInNewTab()
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
