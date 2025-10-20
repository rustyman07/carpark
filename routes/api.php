<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardInventoryController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PaymentController;



Route::get('/cards/{card_id}/transactions', [CardInventoryController::class, 'transactions'])->name('transactions');

Route::get('/card-inventory-search', [ApiController::class, 'search_card_number'])->name('card.inventory.search');
Route::get('/scanned-cards', [CardInventoryController::class, 'getScannedCards'])->name('get.scan.qr.cards');
 Route::get('/payments/search', [ApiController::class, 'search'])->name('payments.search');


Route::get('/tickets/today', [TicketController::class, 'todayTickets']);

?>