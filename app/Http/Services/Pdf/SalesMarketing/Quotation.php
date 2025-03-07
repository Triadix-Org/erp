<?php

namespace App\Http\Services\Pdf\SalesMarketing;

use App\Http\Services\PdfService;
use App\Models\Invoice as ModelsInvoice;
use App\Models\Quotation as ModelsQuotation;

class Quotation
{
    public function __invoke($num)
    {
        $dataQuote = ModelsQuotation::with('details.product', 'customer:id,code,name,pic', 'user:id,name,email,sign')->where('code', $num)->first();
        if (!$dataQuote) {
            abort(404);
        }

        $data = $dataQuote->toArray();
        // dd($data);

        $pdf = PdfService::generate('pdf.sales.quotation', $data);
        return $pdf->stream("Quotation-${num}.pdf");
    }
}
