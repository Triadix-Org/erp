<?php

namespace App\Http\Services\Pdf;

use App\Http\Services\PdfService;
use App\Models\HeaderPurchaseOrder;
use App\Models\HeaderRequestOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;

class PurchaseOrder
{
    public static function generate($poNum)
    {
        $dataPO = HeaderPurchaseOrder::with('detail.product:id,code,name,unit', 'supplier', 'purchaser:id,email,name,sign', 'operational:id,email,name,sign', 'finance:id,email,name,sign')->where('code', $poNum)->first();
        // $company
        $data = $dataPO->toArray();
        // dd($dataPO);
        // $pdf = Pdf::loadView('pdf.production_order', $data);
        $pdf = PdfService::generate('pdf.purchase_order', $data);
        return $pdf->stream('invoice.pdf');
    }
}
