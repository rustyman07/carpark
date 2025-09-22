<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardInventoryController;


Route::get('/cards/{card_id}/transactions', [CardInventoryController::class, 'transactions'])->name('transactions');

Route::post('scan-qr-cards', [CardInventoryController::class, 'scan_qr_cards'])->name('scan.qr.cards');
Route::get('/scanned-cards', [CardInventoryController::class, 'getScannedCards'])->name('get.scan.qr.cards');

?>