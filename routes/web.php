<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('customer.landing');
})->name('landing');

Route::get('/products', function () {
    return view('customer.products');
})->name('products');


