<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('customer.landing', [
        'button' => 'Pesan Sekarang',
    ]);
})->name('landing');

Route::get('/products', [ItemController::class, 'index'])
    ->name('products');

// Route baru untuk chatbot
Route::post('/chatbot', [ItemController::class, 'chatbot'])
    ->name('chatbot');

Route::get('/location/search/', [LocationController::class, 'search'])
    ->name('location.search');

Route::get('/location', [LocationController::class, 'index'])
->name('location');

Route::get('/location/filter', [LocationController::class, 'filter'])
    ->name('location.filter');

Route::get('/products/statistics', [ItemController::class, 'statistics'])
->name('products.statistics');



