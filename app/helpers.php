<?php

if (! function_exists('format_currency')) {
    function format_currency(float $amount, string $currency = 'Rp.'): string
    {
        return  $currency . ' ' . number_format($amount, 0, ',', '.');
    }
}