<?php

namespace App\Http\Services\Pdf;

use App\Http\Services\PdfService;
use App\Models\HeaderMaterialReceived;
use App\Models\HeaderPurchaseOrder;

class MaterialReceivedNote
{
    public static function generate($num)
    {
        $dataMrn = HeaderMaterialReceived::where('code', $num)->first();
        if (!$dataMrn) {
            abort(404);
        }
        // $company
        $data = $dataMrn->toArray();
        // dd($dataPO);
        // $pdf = Pdf::loadView('pdf.production_order', $data);
        $pdf = PdfService::generate('pdf.material_received', $data);
        return $pdf->stream("MRN-${num}.pdf");
    }
}
