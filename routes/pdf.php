<?php

use App\Http\Services\Pdf;

// use App\Http\Services\Pdf\MaterialRequest;
// use App\Http\Services\Pdf\PurchaseOrder;
// use App\Http\Services\Pdf\SalesOrder;
use Illuminate\Support\Facades\Route;

Route::get('/sales/material-request/pdf/{orderNum}', [Pdf\MaterialRequest::class, 'generate'])->name('sales.material-request.pdf');
Route::get('/sales/sales-order/pdf/{orderNum}', [Pdf\SalesOrder::class, 'generate'])->name('sales.sales-order.pdf');
Route::get('/purchasing/purchase-order/pdf/{orderNum}', [Pdf\PurchaseOrder::class, 'generate'])->name('purchase.purchase-order.pdf');
Route::get('/purchasing/material-received-note/pdf/{num}', [Pdf\MaterialReceivedNote::class, 'generate'])->name('purchase.material-received-note.pdf');
