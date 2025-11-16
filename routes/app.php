<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CheckoutController;
use \App\Http\Controllers\CartController;

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

Route::post('/add-to-cart', [ItemController::class, 'addToCart'])
->name('cart.add');

// Cart routes
Route::group(['controller' => CartController::class], function () {
    Route::get('/cart', 'showCart')->name('cart.show');
    Route::get('/cart2', 'showCart2')->name('cart2.show');

    Route::get('/cart-items', 'getCartItems')->name('cart.items');
//    Route::post('/add-to-cart', 'addToCart')->name('cart.add');
    Route::post('/update-cart-quantity', 'updateCartQuantity')->name('cart.update');
    Route::post('/remove-from-cart', 'removeFromCart')->name('cart.remove');
    Route::post('/clear-cart', 'clearCart')->name('cart.clear');
});

Route::group(['controller' => CheckoutController::class], function () {

    Route::get('/checkout', 'index')
    ->name('checkout');

    Route::post('/checkout', 'store')
        ->name('checkout.store');

    Route::get('/history', 'orderHistory')
        ->name('history');
});

// Route chatbot AI DeepSeek
Route::post('/chatbot-ai', [ItemController::class, 'chatbotAI'])
    ->name('chatbot.ai');

// Route chatbot rule-based (existing) - tetap ada
//Route::post('/chatbot', [ItemController::class, 'chatbot'])->name('chatbot');



