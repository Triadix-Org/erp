<?php

namespace App\Http\Services\Pdf;

use App\Http\Services\PdfService;
use App\Models\Invoice;

class PackingList
{
    public function __invoke($num)
    {
        $dataInv = Invoice::with('detail.product', 'customer:id,code,name,pic,address,phone,email', 'user:id,name,email,sign')->where('inv_no', $num)->first();
        if (!$dataInv) {
            abort(404);
        }

        $data = $dataInv->toArray();

        $pdf = PdfService::generate('pdf.packing-list', $data);
        return $pdf->stream('packing-list.pdf');
    }
}
