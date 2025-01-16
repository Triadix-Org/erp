<?php

namespace App\Http\Services\Pdf;

use App\Http\Services\PdfService;
use App\Models\HeaderRequestOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;

class MaterialRequest
{
    public static function generate($orderNum)
    {
        $dataSales = HeaderRequestOrder::with('detail.product:id,code,name,unit', 'req_by:id,email,sign,name', 'approved_by:id,email,sign,name')->where('code', $orderNum)->first();
        // $company
        $data = $dataSales->toArray();
        // dd($data);
        // $pdf = Pdf::loadView('pdf.production_order', $data);
        $pdf = PdfService::generate('pdf.material_request', $data);
        return $pdf->stream('invoice.pdf');
    }
}
