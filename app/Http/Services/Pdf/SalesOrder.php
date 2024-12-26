<?php

namespace App\Http\Services\Pdf;

use App\Http\Services\PdfService;
use App\Models\HeaderRequestOrder;
use App\Models\HeaderSalesOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;

class SalesOrder
{
    public static function generate($orderNum)
    {
        $dataSales = HeaderSalesOrder::with('detail.product:id,code,name,unit,thumbnail,desc', 'sales_by:id,email,sign,name', 'approved_by')->where('code', $orderNum)->first();
        // $company
        $data = $dataSales->toArray();
        // dd($data);
        // $pdf = Pdf::loadView('pdf.production_order', $data);
        $pdf = PdfService::generate('pdf.sales_order', $data);
        return $pdf->stream("production order {$orderNum}.pdf");
    }
}
