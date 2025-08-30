<?php
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardTemplateController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CardInventoryController;


Route::get('/', fn () => Inertia::render('Home/Index'))->name('home');

Route::get('logs', [TicketController::class, 'showLogs'])->name('logs');

Route::resource('parkin',TicketController::class);;

Route::get('parkout', [TicketController::class, 'park_out'])->name('parkout');
Route::post('submit/parkout', [TicketController::class, 'submit_park_out']);
Route::post('submit/payment',[TicketController::class,'submit_payment'])->name('submit.payment');
// Route::post('submit/payment',[TicketController::class,'submit_payment'])->name('submit.payment');
// routes/web.php
Route::post('/verifyQr', [TicketController::class, 'verifyQr'])->name('verify.qr,');
Route::get('/submit/payment/{qr_code}', [TicketController::class, 'submit_payment_qrcode'])->name('submit.payment.qr');


//Route::get('scanQR',[TicketController::class,'scanQR'])->name('scanQR');

Route::resource('card-template', CardTemplateController::class)
    ->only(['index', 'store', 'update']);
// Route::get('cardTemplate',[CardTemplateController::class,'index'])->name('cardTemplate.index');
// Route::post('cardTemplate',[CardTemplateController::class,'store'])->name('cardTemplate.store');
// Route::put('cardTemplate/{id}', [CardTemplateController::class,'update'])->name('cardTemplate.update');



Route::get('card-inventory',[CardInventoryController::class,'index'])->name('card-inventory.index');
 Route::post('card-inventory', [CardInventoryController::class, 'store'])->name('card-inventory.store');

// Route::get('card-inventory', [CardInventoryController::class, 'index'])->name('card-inventory.index');
// Route::post('card-inventory', [CardInventoryController::class, 'store'])->name('card-inventory.store');
// Route::get('card-inventory/{id}', [CardInventoryController::class, 'show'])->name('card-inventory.show');
// Route::put('card-inventory/{id}', [CardInventoryController::class, 'update'])->name('card-inventory.update');
// Route::delete('card-inventory/{id}', [CardInventoryController::class, 'destroy'])->name('card-inventory.destroy');





// Route::get('/parkout', fn () => Inertia::render('Parkout/Index'))->name('parkout');


