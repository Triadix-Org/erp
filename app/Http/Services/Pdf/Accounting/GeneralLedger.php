<?php

namespace App\Http\Services\Pdf\Accounting;

use App\Http\Services\PdfService;

class GeneralLedger
{
    public function __invoke($openBalance)
    {
        $data = [
            'open_balance' => $openBalance
        ];
        $pdf = PdfService::generate('pdf.accounting.general_ledger', $data, 'A4', 'landscape');
        return $pdf->stream("general-ledger-${openBalance}.pdf");
    }
}
