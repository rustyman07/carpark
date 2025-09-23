<?php

use App\Http\Controllers\CardTemplateController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CardInventoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SalesPersonController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 👇 Default home page (guests land here)
Route::get('/', fn () => Inertia::render('Home/Index'))->name('home');

// 👇 Authenticated dashboard (only for logged-in users)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 👇 Profile routes (only for logged-in users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Your custom routes ---
Route::get('logs', [TicketController::class, 'showLogs'])->name('logs');
Route::delete('logs/{id}', [TicketController::class, 'destroy'])->name('logs.delete');

Route::get('parkin', [TicketController::class, 'index'])->name('parkin.index');
Route::post('parkin', [TicketController::class, 'store'])->name('parkin.store');
Route::get('parkin/{uuid}', [TicketController::class, 'show'])->name('parkin.show');

Route::post('/detect-plate', [TicketController::class, 'detect']);

Route::get('parkout', [TicketController::class, 'park_out'])->name('parkout');
Route::post('parkout', [TicketController::class, 'submit_park_out'])->name('parkout.submit');
Route::get('parkout/payment/{uuid}', [TicketController::class, 'show_payment'])->name('show.payment');
Route::post('parkout/payment', [TicketController::class, 'submit_payment'])->name('store.payment');
Route::get('parkout/receipt', [TicketController::class, 'parkout_receipt'])->name('parkout.receipt');

Route::prefix('sales-person')->name('sales-person.')->group(function () {
    Route::get('/', [SalesPersonController::class, 'index'])->name('index');
    Route::post('/', [SalesPersonController::class, 'store'])->name('store');
    Route::put('/{id}', [SalesPersonController::class, 'update'])->name('update');
    Route::delete('/{id}', [SalesPersonController::class, 'destroy'])->name('destroy');
});

Route::get('company', [CompanyController::class, 'index'])->name('company.index');

Route::resource('card-template', CardTemplateController::class)
    ->only(['index', 'store', 'update']);

Route::get('card-inventory', [CardInventoryController::class, 'index'])->name('card-inventory.index');
Route::post('card-inventory', [CardInventoryController::class, 'store'])->name('card-inventory.store');


Route::post('scan-qr-cards', [CardInventoryController::class, 'scan_qr_cards'])->name('scan.qr.cards');

Route::get('sell-card',[CardInventoryController::class,'sell_card'])->name('sell-card.create');
Route::post('sell-card',[CardInventoryController::class,'sell_card_payment'])->name('sell-card.payment');




Route::get('transaction/ticket-payments', [PaymentController::class, 'index'])->name('ticket.payments');

// 👇 Breeze authentication routes (login, register, etc)
require __DIR__.'/auth.php';
