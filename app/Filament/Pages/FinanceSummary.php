<?php

namespace App\Filament\Pages;

use App\Models\Income;
use App\Models\Outcome;
use Filament\Pages\Page;

class FinanceSummary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $title = 'Ringkasan Keuangan';

    protected static string $view = 'filament.pages.finance-summary';

    public $totalIncome;
    public $totalOutcome;
    public $totalProfit;
    public ?string $accountPayables = null;
    public ?string $totalReceivables = null;

    public function mount(): void
    {
        self::getSummary();
    }

    protected function getSummary()
    {
        $income = Income::whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))->sum('amount');
        $formattedIncome = 'Rp ' . number_format($income, 0, ',', '.');
        $this->totalIncome = $formattedIncome;

        $outcome = Outcome::whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))->sum('amount');
        $formattedOutcome = 'Rp ' . number_format($outcome, 0, ',', '.');
        $this->totalOutcome = $formattedOutcome;

        $profit = $income - $outcome;
        $formattedProfit = 'Rp ' . number_format($profit, 0, ',', '.');
        $this->totalProfit = $formattedProfit;

        $accountPayables = \App\Models\AccountsPayable::where('status', 0)
            ->whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))->sum('amount');
        $this->accountPayables = format_currency($accountPayables);

        $totalReceivables = \App\Models\AccountsReceivable::where('status', 2)
            ->whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))->sum('amount');
        $this->totalReceivables = format_currency($totalReceivables);
    }
}
