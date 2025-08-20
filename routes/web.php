<?php
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;


Route::get('/', fn () => Inertia::render('Home/Index'))->name('home');
Route::resource('parkin',TicketController::class);
// Route::get('/parkin', fn () => Inertia::render('Parkin/Index'))->name('parkin');
Route::get('/parkout', fn () => Inertia::render('Parkout/Index'))->name('parkout');
Route::get('/ticketlogs', fn () => Inertia::render('Ticketlogs/Index'))->name('ticketlogs');
