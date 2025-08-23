<?php
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;


Route::get('/', fn () => Inertia::render('Home/Index'))->name('home');
Route::get('logs', [TicketController::class, 'showLogs'])->name('logs');
Route::get('parkout', [TicketController::class, 'park_out'])->name('parkout');
Route::post('parkout', [TicketController::class, 'submit_park_out'])->name('submit.parkout');
// Route::get('logs/search', [TicketController::class, 'search_logs'])->name('search_logs');
Route::resource('parkin',TicketController::class);



// Route::get('/parkout', fn () => Inertia::render('Parkout/Index'))->name('parkout');


