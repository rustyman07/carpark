<?php
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardTemplateController;
use App\Http\Controllers\TicketController;


Route::get('/', fn () => Inertia::render('Home/Index'))->name('home');

Route::get('logs', [TicketController::class, 'showLogs'])->name('logs');

Route::resource('parkin',TicketController::class);;

Route::get('parkout', [TicketController::class, 'park_out'])->name('parkout');
Route::post('submit/parkout', [TicketController::class, 'submit_park_out']);
Route::post('submit/payment',[TicketController::class,'submit_payment'])->name('submit.payment');

//Route::get('scanQR',[TicketController::class,'scanQR'])->name('scanQR');

Route::resource('cardTemplate', CardTemplateController::class)
    ->only(['index', 'store', 'update']);
    
// Route::get('cardTemplate',[CardTemplateController::class,'index'])->name('cardTemplate.index');
// Route::post('cardTemplate',[CardTemplateController::class,'store'])->name('cardTemplate.store');
// Route::put('cardTemplate/{id}', [CardTemplateController::class,'update'])->name('cardTemplate.update');




// Route::get('/parkout', fn () => Inertia::render('Parkout/Index'))->name('parkout');


