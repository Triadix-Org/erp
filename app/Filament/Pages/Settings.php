<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Throwable;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.settings';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?string $title = 'Company Profile';

    public $company_name;
    public $company_code;
    public $brand_name;
    public $address;
    public $phone_one;
    public $phone_two;
    public $email;
    public $fax;
    public $website;

    protected $companyCode;

    public function mount(): void
    {
        $this->companyCode = env('COMPANY_CODE');
        $setting = Setting::where('company_code', $this->companyCode)->first();

        if ($setting) {
            // Isi data ke properti publik
            $this->company_name = $setting->company_name;
            $this->company_code = $setting->company_code;
            $this->brand_name = $setting->brand_name;
            $this->address = $setting->address;
            $this->phone_one = $setting->phone_one;
            $this->phone_two = $setting->phone_two;
            $this->email = $setting->email;
            $this->fax = $setting->fax;
            $this->website = $setting->website;
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Card::make()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('company_name')
                                ->label('Company Name')
                                ->required()
                                ->default($this->company_name),
                            TextInput::make('company_code')
                                ->label('Company Code')
                                ->length(2)
                                ->required()
                                ->readOnly()
                                ->default($this->company_code),
                            TextInput::make('brand_name')
                                ->label('Brand Name')
                                ->required()
                                ->default($this->brand_name),
                            TextInput::make('address')
                                ->label('Address')
                                ->required()
                                ->default($this->address),
                            TextInput::make('phone_one')
                                ->label('First Phone')
                                ->required()
                                ->placeholder('Please use country code (62)')
                                ->numeric()
                                ->default($this->phone_one),
                            TextInput::make('phone_two')
                                ->label('Second Phone')
                                ->numeric()
                                ->placeholder('Please use country code (62)')
                                ->default($this->phone_two),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->default($this->email),
                            TextInput::make('fax')
                                ->numeric()
                                ->default($this->fax),
                            TextInput::make('website')
                                ->default($this->website),
                        ])
                ])
        ];
    }

    public function submitForm(): void
    {
        try {
            $setting = Setting::where('company_code', $this->company_code)->first();

            if ($setting) {
                // Update data
                $setting->update([
                    'company_name' => $this->company_name,
                    'company_code' => $this->company_code,
                    'brand_name' => $this->brand_name,
                    'address' => $this->address,
                    'phone_one' => $this->phone_one,
                    'phone_two' => $this->phone_two,
                    'email' => $this->email,
                    'fax' => $this->fax,
                    'website' => $this->website,
                ]);
            }
            Notification::make()
                ->title('Saved Successfully!')
                ->success()
                ->send();
        } catch (Throwable $th) {
            Notification::make()
                ->title('Opps.. Something went wrong!')
                ->danger()
                ->send();
        }
    }
}
