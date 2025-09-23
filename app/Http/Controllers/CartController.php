<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
//    public function index(){
//
//        $session = Session::get('cart');
//
//        return view('customer.cart');
//    }

    // Method untuk menampilkan halaman cart
    public function showCart()
    {
        $cart = Session::get('cart');

        return view('customer.cart', compact('cart'));
    }

    // Method untuk mendapatkan items cart via AJAX
    public function getCartItems()
    {
        $cart = Session::get('cart', []);

        return response()->json([
            'status' => 'success',
            'cart' => $cart,
            'cart_count' => count($cart)
        ]);
    }

    // Method untuk update quantity item di cart
    public function updateCartQuantity(Request $request)
    {
        $item_id = $request->input('item_id');
        $berat_kg = $request->input('berat_kg');

        // Validasi input
        if (!$item_id || !$berat_kg || $berat_kg < 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid'
            ], 400);
        }

        $cart = Session::get('cart', []);

        if (!isset($cart[$item_id])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item tidak ditemukan di keranjang'
            ], 404);
        }

        // Update berat
        $cart[$item_id]['berat_kg'] = $berat_kg;
        Session::put('cart', $cart);

        return response()->json([
            'status' => 'success',
            'message' => 'Jumlah berhasil diupdate',
            'cart' => $cart
        ]);
    }

    // Method untuk menghapus item dari cart
    public function removeFromCart(Request $request)
    {
        $item_id = $request->input('item_id');

        if (!$item_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item ID diperlukan'
            ], 400);
        }

        $cart = Session::get('cart', []);

        if (!isset($cart[$item_id])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item tidak ditemukan di keranjang'
            ], 404);
        }

        // Hapus item dari cart
        unset($cart[$item_id]);
        Session::put('cart', $cart);

        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil dihapus dari keranjang',
            'cart' => $cart,
            'cart_count' => count($cart)
        ]);
    }

    // Method untuk clear semua cart
    public function clearCart()
    {
        Session::forget('cart');

        return response()->json([
            'status' => 'success',
            'message' => 'Keranjang berhasil dikosongkan'
        ]);
    }
}
