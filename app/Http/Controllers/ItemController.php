<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ItemController extends Controller
{
    public function index(){
        $items = Item::all();

        $defaultItem = Item::where('id', 'pkcg')->first() ??
                      Item::first() ??
                      null;

        return view('customer.products', [
            'items' => $items,
            'defaultItem' => $defaultItem
        ]);
    }
    public function chatbot(Request $request){
        $message = strtolower($request->input('message'));
        $response = '';
        $products = [];

        // Keywords untuk produk terlaris
        $terlaris_keywords = ['terlaris', 'paling banyak', 'populer', 'favorit', 'best seller', 'paling laku', 'recommended'];

        // Keywords untuk rasa asin gurih (Teri & Rebon)
        $asin_keywords = ['asin', 'gurih asin', 'seafood', 'laut', 'teri', 'rebon', 'udang', 'ikan'];

        // Keywords untuk rasa gurih kacang
        $kacang_keywords = ['gurih','gurih kacang', 'kacang', 'kedelai', 'kacang hijau', 'kacang tanah'];

        // Cek produk terlaris
        if ($this->containsKeywords($message, $terlaris_keywords)) {
            // Mencari produk terlaris
            $products = Item::whereIn('id', ['pkdl', 'pkhj'])->get();
            $response = 'Produk terlaris kami adalah Peyek Kedelai dan Peyek Kacang Hijau! Kedua varian ini paling banyak dipesan karena citarasanya yang gurih dan berkualitas.';
        }
        // Cek rasa asin gurih
        elseif ($this->containsKeywords($message, $asin_keywords)) {
            $products = Item::whereIn('id', ['ptr', 'pur'])->get();
            $response = 'Untuk rasa asin gurih khas ikan asin, kami rekomendasikan Peyek Teri dan Peyek Rebon. Keduanya memberikan citarasa asin gurih yang khas dari seafood berkualitas!';
        }
        // Cek rasa gurih kacang
        elseif ($this->containsKeywords($message, $kacang_keywords)) {
            $products = Item::whereIn('id', ['pkcg', 'pkdl', 'pkhj'])->get();
            $response = 'Untuk rasa gurih, kami punya Peyek Kacang Tanah, Peyek Kedelai, dan Peyek Kacang Hijau. Semuanya memberikan kelezatan kacang yang autentik dan gurih!';
        }
        // Keywords lainnya
        elseif (str_contains($message, 'harga') || str_contains($message, 'price')) {
            $products = Item::all();
            $response = 'Berikut daftar harga peyek kami: Peyek Kacang Rp50.000/kg, Peyek Kedelai Rp52.000/kg, Peyek Kacang Hijau Rp52.000/kg, Peyek Teri Rp56.000/kg, dan Peyek Rebon Rp60.000/kg.';
        }
        elseif (str_contains($message, 'cara pesan') || str_contains($message, 'order') || str_contains($message, 'beli')) {
            $response = 'Cara pesan sangat mudah! 1) Pilih varian peyek yang diinginkan, 2) Atur jumlah dalam kilogram, 3) Klik tombol Checkout, 4) Isi data pengiriman. Tim kami akan segera memproses pesanan Anda!';
        }
        elseif (str_contains($message, 'info') || str_contains($message, 'produk')) {
            $products = Item::all();
            $response = 'Kami menyediakan 5 varian peyek berkualitas: Peyek Kacang Tanah, Peyek Kedelai, Peyek Kacang Hijau, Peyek Teri, dan Peyek Rebon. Semua dibuat dengan bahan pilihan dan bumbu tradisional!';
        }
        elseif (str_contains($message, 'cara pesan')){
            $response = '1)Lihat Produk \n 2)Pilih Item yang diinginkan \n 3)Atur Jumlah \n 4)Checkout';
        }
        else {
            $response = 'Maaf, saya belum memahami pertanyaan Anda. Anda bisa bertanya tentang produk terlaris, rasa asin gurih, rasa gurih kacang, harga, atau cara pemesanan. Ada yang bisa saya bantu lagi?';
        }

        // Format response dengan produk jika ada
        $formatted_products = [];
        if ($products->count() > 0) {
            foreach ($products as $product) {
                $formatted_products[] = [
                    'id' => $product->id,
                    'nama' => $product->nama_peyek,
                    'topping' => $product->topping,
                    'harga' => 'Rp' . number_format($product->hrg_kiloan, 0, ',', '.') . '/kg',
                    'deskripsi' => $product->deskripsi,
                    'gambar' => asset('img_item_upload/' . $product->gambar)
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => $response,
            'products' => $formatted_products,
            'total_products' => count($formatted_products)
        ]);
    }

    private function containsKeywords($message, $keywords) {
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }
        return false;
    }

    public function statistics(){

        $count = Item::count(); // total item
        $total_price = Item::sum('hrg_kiloan'); // total harga
        $max_price = Item::max('hrg_kiloan'); //harga tertinggi
        $min_price = Item::min('hrg_kiloan'); //harga terendah

        return view('praktikum.soal4', compact(
                'count', 'total_price',
                'max_price', 'min_price'
            )
        );
    }

    public function addToCart(Request $request){
        $item_id = $request->item_id;
        $berat_kg = $request->berat_kg;

        // Validasi input
        if(!$berat_kg){
            return response()->json([
                'status' => 'error',
                'message' => 'Berat tidak valid'
            ]);
        }

        if(!$item_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Item ID tidak valid'
            ], 400);
        }


//        if (!$item_id || !$berat_kg) {
//            return response()->json([
//                'status' => 'error',
//                'message' => 'Data tidak lengkap'
//            ], 400);
//        }

        $item = Item::find($item_id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item tidak ditemukan'
            ], 404);
        }

        $cart = Session::get('cart', []);

        $currCart = $cart[$item_id] ?? null;

        // jika cart sudah ada, tambahkan berat
        if ($currCart) {
            $currCart['berat_kg'] += $berat_kg;
        }
        // jika belum ada, tambahkan ke cart
        else {
            $currCart = [
                'id' => $item->id,
                'nama' => $item->nama_peyek,
                'topping' => $item->topping,
                'harga' => $item->hrg_kiloan,
                'gambar' => asset('img_item_upload/' . $item->gambar),
                'berat_kg' => $berat_kg,
            ];
        }

        $cart[$item_id] = $currCart;
        Session::put('cart', $cart);

        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil ditambahkan ke keranjang',
            'cart' => $cart,
            'cart_count' => count($cart)
        ], 200);
    }
}
