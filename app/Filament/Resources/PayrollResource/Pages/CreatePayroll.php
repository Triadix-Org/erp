<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use App\Models\Employee;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;

    public function mount(): void
    {
        parent::mount();

        // dd($this->getEmployeeDetails());
        $this->form->fill([
            'detail' => $this->getEmployeeDetails(),
        ]);
    }

    protected function getEmployeeDetails(): array
    {
        // Ambil data Employee dan sesuaikan dengan field TableRepeater
        $employees = Employee::with('personnel')->get();

        return $employees->map(function ($employee) {
            return [
                'employee_id' => $employee->id,
                'name'        => $employee->name,
                'salary'      => $employee->personnel?->sallary,
                'overtime'    => 0,
                'bonus'       => 0,
                'cut'         => 0,
                'total'       => 0
            ];
        })->toArray();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel());
        $record->month = $data['month'];
        $record->year = $data['year'];
        $record->save();

        foreach ($data['detail'] as $key => $detail) {
            $detail = $record->detail()->create([
                'payroll_id'    => $record->id,
                'employee_id'   => $detail['employee_id'],
                'salary'        => $detail['salary'],
                'overtime'      => $detail['overtime'],
                'bonus'         => $detail['bonus'],
                'cut'           => $detail['cut'],
                'total'         => $detail['total'],
            ]);
        }
        return $record;
    }
}
