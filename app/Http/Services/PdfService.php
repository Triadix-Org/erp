<?php

namespace App\Http\Services;

use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public static function generate($view, $data, $paper = 'A4', $orientation = 'portrait')
    {
        $company = Setting::first();
        $data['company'] = $company;
        // dd($data);
        return Pdf::loadView($view, $data)
            ->setPaper($paper, $orientation);
    }

    public static function calculateSize($num)
    {
        return $num / 2.54 * 72;
    }
}
