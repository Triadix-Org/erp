<?php

namespace App\Http\Services\Pdf;

use App\Http\Services\PdfService;
use App\Models\DetailPayroll;

class PaySlip
{
    public function __invoke($id)
    {
        $dataPayroll = DetailPayroll::with('payroll', 'employee.personnel.dept:id,name', 'employee.personnel.div:id,name')->find($id);

        $data = $dataPayroll->toArray();

        $pdf = PdfService::generate('pdf.hr.pay-slip', $data);
        return $pdf->stream('invoice.pdf');
    }
}
