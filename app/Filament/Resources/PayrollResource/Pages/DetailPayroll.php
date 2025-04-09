<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use App\Models\DetailPayroll as ModelsDetailPayroll;
use App\Models\Payroll;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DetailPayroll extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = PayrollResource::class;

    protected static string $view = 'filament.resources.payroll-resource.pages.detail-payroll';

    public $record;
    public $month;
    public $year;
    public $status;
    public $paidDate;

    public function mount($record): void
    {
        $this->record = $record;
        $payrollData = Payroll::find($record);
        $this->month = $payrollData->month;
        $this->year = $payrollData->year;
        $this->status = $payrollData->status;
        $this->paidDate = $payrollData->paidDate;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('month')->readOnly(),
                                TextInput::make('year')->readOnly(),
                            ])
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                TextColumn::make('employee.name')
                    ->searchable(),
                TextColumn::make('salary')
                    ->sortable()
                    ->numeric()
                    ->prefix("Rp. "),
                TextColumn::make('overtime')
                    ->sortable()
                    ->numeric()
                    ->prefix("Rp. "),
                TextColumn::make('bonus')
                    ->sortable()
                    ->numeric()
                    ->prefix("Rp. "),
                TextColumn::make('cut')
                    ->sortable()
                    ->numeric()
                    ->prefix("Rp. "),
                TextColumn::make('total')
                    ->sortable()
                    ->numeric()
                    ->prefix("Rp. "),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('pdf')->color('primary')->icon('heroicon-s-document-currency-dollar')->label('Pay Slip'),
                    Action::make('mail')->color('info')->icon('heroicon-s-envelope-open')->label('Send Email'),
                ])
            ], position: ActionsPosition::BeforeColumns);
    }

    protected function getFilteredQuery(): Builder
    {
        return ModelsDetailPayroll::where('payroll_id', $this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionsAction::make('sendMail')
                ->label('Send to Mail')
                ->color('primary')
                ->icon('heroicon-s-envelope'),
            ActionsAction::make('exportExcel')
                ->label('Export')
                ->color('info')
                ->icon('heroicon-s-document-plus'),
        ];
    }
}
