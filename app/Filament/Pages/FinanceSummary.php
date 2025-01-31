<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class FinanceSummary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Finance';

    protected static string $view = 'filament.pages.finance-summary';
}
