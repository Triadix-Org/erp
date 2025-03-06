<?php

namespace App\Http\Services\Pdf;

use App\Http\Services\PdfService;
use App\Models\HeaderMaterialReceived;
use App\Models\HeaderPurchaseOrder;
use App\Models\Invoice as ModelsInvoice;

class Invoice
{
    public static function proforma($num)
    {
        $dataInv = ModelsInvoice::with('detail.product', 'headerSalesOrder:id,code', 'customer:id,code,name,pic', 'user:id,name,email,sign')->where('inv_no', $num)->first();
        if (!$dataInv) {
            abort(404);
        }

        $data = $dataInv->toArray();
        // dd($data);

        $pdf = PdfService::generate('pdf.invoice.proforma', $data);
        return $pdf->stream("Proforma-Invoice-${num}.pdf");
    }

    public static function commercial($num)
    {
        $dataInv = ModelsInvoice::with('detail.product', 'headerSalesOrder:id,code', 'customer:id,code,name,pic', 'user:id,name,email,sign')->where('inv_no', $num)->first();
        if (!$dataInv) {
            abort(404);
        }

        $data = $dataInv->toArray();
        // dd($data);

        $pdf = PdfService::generate('pdf.invoice.commercial', $data);
        return $pdf->stream("Commercial-Invoice-${num}.pdf");
    }
}
