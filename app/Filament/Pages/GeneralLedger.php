<?php

namespace App\Filament\Pages;

use App\Models\AccountingPeriods;
use App\Models\ChartOfAccount;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class GeneralLedger extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.general-ledger';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 3;

    public $periods;
    public $openBalance = 0;
    public $coa;

    public function mount()
    {
        $this->periods = AccountingPeriods::open()?->first()->id;
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    Section::make()
                        ->columnSpan(1)
                        ->schema([
                            Grid::make()
                                ->schema([
                                    Select::make('accounting_periods')
                                        ->label('Periode')
                                        ->options(AccountingPeriods::pluck('name', 'id'))
                                        ->default($this->periods)
                                        ->live()
                                        ->afterStateUpdated(function ($state) {
                                            $this->periods = $state;
                                        })
                                        ->required(),
                                    Select::make('include_open_balance')
                                        ->label('Tampilkan Opening Balance')
                                        ->default($this->openBalance)
                                        ->reactive()
                                        ->afterStateUpdated(fn($state) => $this->openBalance = $state)
                                        ->options([
                                            1 => 'Ya',
                                            0 => 'Tidak',
                                        ])
                                ]),
                            Select::make('chart_of_account_id')
                                ->reactive()
                                ->default($this->coa)
                                ->label('CoA')
                                ->options(ChartOfAccount::pluck('name', 'id'))
                                ->multiple()
                                ->helperText('Kosongkan jika ingin memilih semua')
                        ])
                ])
        ];
    }
}
