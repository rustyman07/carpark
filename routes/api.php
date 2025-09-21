<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardInventoryController;


Route::get('/cards/{card_id}/transactions', [CardInventoryController::class, 'transactions'])->name('transactions');

?>