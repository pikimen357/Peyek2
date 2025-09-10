<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('customer.landing', [
        'button' => 'Pesan Sekarang',
    ]);
})->name('landing');

Route::get('/products', [ItemController::class, 'index'])
->name('products');


