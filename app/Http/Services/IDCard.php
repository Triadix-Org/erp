<?php

namespace App\Http\Services;

use App\Http\Services\PdfService;
use App\Models\Employee;
use App\Models\HeaderMaterialReceived;
use App\Models\HeaderPurchaseOrder;

class IDCard
{
    public function __invoke($nip)
    {
        $dataEmpl = Employee::where('nip', $nip)->first();
        if (!$dataEmpl) {
            abort(404);
        }
        // $company
        $data = $dataEmpl->toArray();
        $width = PdfService::calculateSize(10);
        $height = PdfService::calculateSize(10);
        $paper = array(0, 0, $width, $height);
        // $paper = [$width, $height];
        $pdf = PdfService::generate('pdf.id_card', $data, $paper);
        return $pdf->stream("ID Card {$nip}.pdf");
    }
}
