<?php
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;


Route::get('/', fn () => Inertia::render('Home/Index'))->name('home');
// Route::get('parkin/logs', [TicketController::class, 'showLogs']);
Route::get('logs', [TicketController::class, 'showLogs'])->name('logs');
Route::resource('parkin',TicketController::class);
// Route::get('/parkin', fn () => Inertia::render('Parkin/Index'))->name('parkin');
Route::get('/parkout', fn () => Inertia::render('Parkout/Index'))->name('parkout');
// Route::get('/logs', fn () => Inertia::render('Ticketlogs/Index'))->name('ticketlogs');
