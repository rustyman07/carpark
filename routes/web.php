<?php
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardTemplateController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CardInventoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SalesPersonController;


Route::get('/', fn () => Inertia::render('Home/Index'))->name('home');

Route::get('logs', [TicketController::class, 'showLogs'])->name('logs');
Route::delete('logs/{id}' ,[TicketController::class,'destroy'])->name('logs.delete');

Route::get('parkin', [TicketController::class, 'index'])->name('parkin.index');
Route::post('parkin', [TicketController::class, 'store'])->name('parkin.store');
Route::get('parkin/{uuid}', [TicketController::class, 'show'])->name('parkin.show');

// routes/web.php
Route::post('/detect-plate', [TicketController::class, 'detect']);


Route::get('parkout', [TicketController::class, 'park_out'])->name('parkout');
Route::post('parkout', [TicketController::class, 'submit_park_out'])->name('parkout.submit');
Route::get('parkout/payment/{uuid}',[TicketController::class,'show_payment'])->name('show.payment');
Route::post('parkout/payment',[TicketController::class,'submit_payment'])->name('store.payment');
// Route::post('process-qr-Payment', [TicketController::class, 'processQrPayment'])->name('process.qr');
Route::get('parkout/receipt',[TicketController::class,'parkout_receipt'])->name('parkout.receipt');


Route::post('scan-qr-cards', [TicketController::class, 'scan_qr_cards'])->name('scan.qr.cards');

Route::prefix('sales-person')->name('sales-person.')->group(function () {
    Route::get('/', [SalesPersonController::class, 'index'])->name('index');
    Route::post('/', [SalesPersonController::class, 'store'])->name('store');
    Route::put('/{id}', [SalesPersonController::class, 'update'])->name('update');
    Route::delete('/{id}', [SalesPersonController::class, 'destroy'])->name('destroy');
});



Route::get('company',[CompanyController::class,'index'])->name('company.index');

Route::resource('card-template', CardTemplateController::class)
    ->only(['index', 'store', 'update']);


// Route::get('cardTemplate',[CardTemplateController::class,'index'])->name('cardTemplate.index');
// Route::post('cardTemplate',[CardTemplateController::class,'store'])->name('cardTemplate.store');
// Route::put('cardTemplate/{id}', [CardTemplateController::class,'update'])->name('cardTemplate.update');

Route::get('card-inventory',[CardInventoryController::class,'index'])->name('card-inventory.index');
Route::post('card-inventory', [CardInventoryController::class, 'store'])->name('card-inventory.store');



Route::get('transaction/ticket-payments',[PaymentController::class, 'index'])->name('ticket.payments');

// Route::get('card-inventory', [CardInventoryController::class, 'index'])->name('card-inventory.index');
// Route::post('card-inventory', [CardInventoryController::class, 'store'])->name('card-inventory.store');
// Route::get('card-inventory/{id}', [CardInventoryController::class, 'show'])->name('card-inventory.show');
// Route::put('card-inventory/{id}', [CardInventoryController::class, 'update'])->name('card-inventory.update');
// Route::delete('card-inventory/{id}', [CardInventoryController::class, 'destroy'])->name('card-inventory.destroy');





// Route::get('/parkout', fn () => Inertia::render('Parkout/Index'))->name('parkout');


