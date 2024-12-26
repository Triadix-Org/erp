<?php

use App\Http\Services\Pdf\MaterialRequest;
use App\Http\Services\Pdf\SalesOrder;
use Illuminate\Support\Facades\Route;

Route::get('/sales/material-request/pdf/{orderNum}', [MaterialRequest::class, 'generate'])->name('sales.material-request.pdf');
Route::get('/sales/sales-order/pdf/{orderNum}', [SalesOrder::class, 'generate'])->name('sales.sales-order.pdf');
