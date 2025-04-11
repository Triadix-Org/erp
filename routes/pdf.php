<?php

use App\Http\Services\IDCard;
use App\Http\Services\Pdf;

// use App\Http\Services\Pdf\MaterialRequest;
// use App\Http\Services\Pdf\PurchaseOrder;
// use App\Http\Services\Pdf\SalesOrder;
use Illuminate\Support\Facades\Route;

Route::get('/purchasing/purchase-order/pdf/{orderNum}', [Pdf\PurchaseOrder::class, 'generate'])->name('purchase.purchase-order.pdf');
Route::get('/purchasing/material-received-note/pdf/{num}', [Pdf\MaterialReceivedNote::class, 'generate'])->name('purchase.material-received-note.pdf');
Route::get('/human-resource/id-card/{nip}', IDCard::class)->name('human-resource.id-card');

// Sales & Marketing
Route::get('/sales/material-request/pdf/{orderNum}', [Pdf\MaterialRequest::class, 'generate'])->name('sales.material-request.pdf');
Route::get('/sales/sales-order/pdf/{orderNum}', [Pdf\SalesOrder::class, 'generate'])->name('sales.sales-order.pdf');
Route::get('/sales/proforma-invoice/pdf/{num}', [Pdf\Invoice::class, 'proforma'])->name('sales.proforma-invoice.pdf');
Route::get('/sales/commercial-invoice/pdf/{num}', [Pdf\Invoice::class, 'commercial'])->name('sales.commercial-invoice.pdf');
Route::get('/sales/quotation/pdf/{num}', Pdf\SalesMarketing\Quotation::class)->name('sales.quotation.pdf');

Route::get('/sales/packing-list/pdf/{num}', Pdf\PackingList::class)->name('sales.packing-list.pdf');
Route::get('/sales/shipping-marks/pdf/{num}', Pdf\ShippingMark::class)->name('sales.shipping-marks.pdf');

// Human Resource
Route::get('/hr/pay-slip/pdf/{id}', Pdf\PaySlip::class)->name('hr.pay-slip.pdf');

// Accounting
Route::get('/accounting/general-ledger/pdf/{openBalance}/{periods}', Pdf\Accounting\GeneralLedger::class)->name('accounting.general-ledger');
