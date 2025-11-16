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

// Route chatbot hybrid (utama)
Route::post('/chatbot-hybrid', [ItemController::class, 'chatbotHybrid'])->name('chatbot.hybrid');

// Route untuk debugging
Route::post('/chatbot-debug', [ItemController::class, 'chatbotDebug'])->name('chatbot.debug');

// Route chatbot rule-based (existing) - tetap ada
Route::post('/chatbot', [ItemController::class, 'chatbot'])->name('chatbot');

// Route chatbot rule-based (existing) - tetap ada
//Route::post('/chatbot', [ItemController::class, 'chatbot'])->name('chatbot');

// routes/web.php - tambahkan route test sementara
Route::get('/test-deepseek', function() {
    try {
        $deepSeekService = app(\App\Services\DeepSeekService::class);

        // Test koneksi API
        echo "Testing DeepSeek Service...\n";
        echo "API Key: " . (env('DEEPSEEK_API_KEY') ? '✅ Set' : '❌ Not Set') . "\n";

        // Test pertanyaan sederhana
        $testQuestion = "Halo, apa kabar?";
        echo "Test Question: " . $testQuestion . "\n";

        $response = $deepSeekService->handleCustomerQuestion($testQuestion);

        echo "✅ Service Response: " . $response . "\n";
        return response()->json(['success' => true, 'response' => $response]);

    } catch (\Exception $e) {
        echo "❌ Service Error: " . $e->getMessage() . "\n";
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

