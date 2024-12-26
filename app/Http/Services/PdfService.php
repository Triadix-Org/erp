<?php

namespace App\Http\Services;

use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public static function generate($view, $data)
    {
        $company = Setting::where('company_code', 'TF')->first();
        $data['company'] = $company;
        // dd($data);
        return Pdf::loadView($view, $data);
    }
}
