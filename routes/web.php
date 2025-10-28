<?php

use App\Http\Controllers\CardTemplateController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CardInventoryController;
use App\Http\Controllers\ShiftLogController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;                                        
use App\Http\Controllers\SalesPersonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;




Route::middleware(['auth'])->group(function () {

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('logs', [TicketController::class, 'showLogs'])->name('logs');
    Route::delete('logs/{id}', [TicketController::class, 'destroy'])->name('logs.delete');

    Route::get('parkin', [TicketController::class, 'index'])->name('parkin.index');
    Route::post('parkin', [TicketController::class, 'store'])->name('parkin.store');
    Route::get('parkin/{uuid}', [TicketController::class, 'show'])->name('parkin.show');
    Route::get('parkin/print/{uuid}', [TicketController::class, 'print_ticket'])->name('print.ticket');


    Route::get('parkout', [TicketController::class, 'park_out'])->name('parkout');
    Route::post('parkout', [TicketController::class, 'submit_park_out'])->name('parkout.submit');
    Route::get('parkout/payment/{uuid}', [TicketController::class, 'show_payment'])->name('show.payment');
    Route::post('parkout/payment', [TicketController::class, 'submit_payment'])->name('store.payment');


    // Route::prefix('sales-person')->name('sales-person.')->group(function () {
    //     Route::get('/', [SalesPersonController::class, 'index'])->name('index');
    //     Route::post('/', [SalesPersonController::class, 'store'])->name('store');
    //     Route::put('/{id}', [SalesPersonController::class, 'update'])->name('update');
    //     Route::delete('/{id}', [SalesPersonController::class, 'destroy'])->name('destroy');
    // });

    Route::get('company', [CompanyController::class, 'index'])->name('company.index');
    Route::put('company/{id}', [CompanyController::class, 'update'])->name('company.update');

    Route::resource('card-template', CardTemplateController::class)->only(['index', 'store', 'update']);

    Route::get('card-inventory', [CardInventoryController::class, 'index'])->name('card-inventory.index');
    Route::post('card-inventory', [CardInventoryController::class, 'store'])->name('card-inventory.store');
    Route::post('scan-qr-cards', [CardInventoryController::class, 'scan_qr_cards'])->name('scan.qr.cards');
    Route::get('/card/print/{uuid}', [CardInventoryController::class, 'print_card'])->name('print.card');
    Route::get('sell-card', [CardInventoryController::class, 'sell_card'])->name('sell-card.create');
    Route::post('sell-card', [CardInventoryController::class, 'sell_card_payment'])->name('sell-card.payment'); 
    Route::put('/card-inventory/{id}/status', [CardInventoryController::class, 'updateStatus'])->name('card-inventory.update-status');

    // Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    // Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
    // Route::put('/shifts/{shift}', [ShiftController::class, 'update'])->name('shifts.update');
    // Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

    // Route::post('/shiftlogs/start', [ShiftLogController::class, 'startShift'])->name('shiftlogs.start');
    // Route::post('/shiftlogs/end', [ShiftLogController::class, 'endShift'])->name('shiftlogs.end');

    Route::get('transaction', [PaymentController::class, 'index'])->name('payments.index');

    Route::get('/reports/parkout/preview', [ReportController::class, 'previewPDF'])
    ->name('reports.parkout.preview');
      Route::get('/reports/transaction/preview', [PaymentController::class, 'generatePaymentReport'])->name('reports.transaction.preview');

    // routes/web.php
    Route::get('/receipt/print/{uuid}', [ReceiptController::class, 'printReceipt'])->name('receipt.print');
    Route::get('parkout/receipt', [ReceiptController::class, 'index'])->name('receipt.index');

});

require __DIR__.'/auth.php';
