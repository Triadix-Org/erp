<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use App\Models\DetailPayroll;
use App\Models\Payroll;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class EditPayroll extends EditRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Ambil record Payroll dengan detail dan employee
        $payroll = Payroll::with('detail.employee')->findOrFail($record);

        // Pastikan kita hanya memproses 'detail', bukan seluruh payroll
        $details = $payroll->detail->map(function ($detail) {
            return [
                'id' => $detail->id,
                'employee_id' => $detail->employee_id,
                'overtime' => $detail->overtime,
                'bonus' => $detail->bonus,
                'cut' => $detail->cut,
                'total' => $detail->total,
                'salary' => $detail->salary,
                'name' => optional($detail->employee)->name, // Ambil nama employee jika ada
            ];
        })->toArray();

        // Simpan data detail ke dalam array dengan kunci yang unik
        $this->data['detail'] = Arr::mapWithKeys($details, function ($detail) {
            return [
                'record-' . $detail['id'] => $detail,
            ];
        });
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (!empty($data['detail'])) {
            foreach ($data['detail'] as $details) {
                if ($details) {
                    $detail = DetailPayroll::find($details['id']);
                    if ($detail) {
                        $detail->overtime = $details['overtime'];
                        $detail->bonus = $details['bonus'];
                        $detail->bonus = $details['bonus'];
                        $detail->cut = $details['cut'];
                        $detail->total = $details['total'];
                        $detail->save();
                    }
                }
            }
        }

        return $record;
    }
}
