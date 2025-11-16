<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\DeepSeekService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    protected $deepSeekService;

    public function __construct(DeepSeekService $deepSeekService)
    {
        $this->deepSeekService = $deepSeekService;
    }

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

    // METHOD HYBRID: Otomatis pilih antara rule-based atau AI
    public function chatbotHybrid(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        try {
            $message = strtolower($request->input('message'));

            // 1. Cek apakah pertanyaan bisa dihandle oleh rule-based
            $ruleBasedResponse = $this->processRuleBased($message);
            if ($ruleBasedResponse['handled']) {
                return response()->json([
                    'status' => 'success',
                    'message' => $ruleBasedResponse['message'],
                    'products' => $ruleBasedResponse['products'],
                    'total_products' => count($ruleBasedResponse['products']),
                    'type' => 'rule-based',
                    'response_time' => $ruleBasedResponse['response_time']
                ]);
            }

            // 2. Jika rule-based tidak bisa handle, gunakan AI
            $startTime = microtime(true);
            $aiResponse = $this->deepSeekService->handleCustomerQuestion($message);
            $responseTime = round((microtime(true) - $startTime) * 1000, 2); // dalam ms

            return response()->json([
                'status' => 'success',
                'message' => $aiResponse,
                'products' => [],
                'total_products' => 0,
                'type' => 'ai',
                'response_time' => $responseTime
            ]);

        } catch (\Exception $e) {
            // Fallback ke rule-based jika AI error
            $fallbackResponse = $this->processRuleBased($request->input('message') ?? 'help');

            return response()->json(
                $fallbackResponse['handled'] ? [
                    'status' => 'success',
                    'message' => $fallbackResponse['message'],
                    'products' => $fallbackResponse['products'],
                    'total_products' => count($fallbackResponse['products']),
                    'type' => 'rule-based-fallback',
                    'response_time' => $fallbackResponse['response_time']
                ] : [
                    'status' => 'error',
                    'message' => 'Maaf, sedang ada gangguan. Silakan coba lagi.',
                    'products' => [],
                    'total_products' => 0
                ]
            );
        }
    }

    // METHOD: Process rule-based questions
    private function processRuleBased($message)
    {
        $startTime = microtime(true);
        $response = '';
        $products = [];
        $handled = true;

        // Keywords untuk produk terlaris
        $terlaris_keywords = ['terlaris', 'paling banyak', 'populer', 'favorit', 'best seller', 'paling laku', 'recommended'];

        // Keywords untuk rasa asin gurih (Teri & Rebon)
        $asin_keywords = ['asin', 'gurih asin', 'seafood', 'laut', 'teri', 'rebon', 'udang', 'ikan'];

        // Keywords untuk rasa gurih kacang
        $kacang_keywords = ['gurih','gurih kacang', 'kacang', 'kedelai', 'kacang hijau', 'kacang tanah'];

        // Keywords untuk sapaan sederhana
        $greeting_keywords = ['hai', 'halo', 'hello', 'hi', 'selamat', 'pagi', 'siang', 'sore', 'malam'];

        // Keywords untuk terima kasih
        $thanks_keywords = ['terima kasih', 'thanks', 'thank you', 'makasih'];

        // Keywords untuk perpisahan
        $bye_keywords = ['bye', 'dadah', 'selesai', 'sampai jumpa', 'goodbye'];

        // Cek sapaan sederhana
        if ($this->containsAny($message, $greeting_keywords)) {
            $response = 'Halo! 👋 Selamat datang di PeyekKu. Ada yang bisa saya bantu? Anda bisa bertanya tentang produk, harga, atau cara pesan.';
        }
        // Cek terima kasih
        elseif ($this->containsAny($message, $thanks_keywords)) {
            $response = 'Sama-sama! 😊 Terima kasih sudah menghubungi PeyekKu. Semoga harimu menyenangkan!';
        }
        // Cek perpisahan
        elseif ($this->containsAny($message, $bye_keywords)) {
            $response = 'Terima kasih sudah berkunjung! 👋 Jangan lupa coba peyek kami ya. Sampai jumpa!';
        }
        // Cek produk terlaris
        elseif ($this->containsAny($message, $terlaris_keywords)) {
            $products = Item::whereIn('id', ['pkdl', 'pkhj'])->get();
            $response = 'Produk terlaris kami adalah Peyek Kedelai dan Peyek Kacang Hijau! Kedua varian ini paling banyak dipesan karena citarasanya yang gurih dan berkualitas.';
        }
        // Cek rasa asin gurih
        elseif ($this->containsAny($message, $asin_keywords)) {
            $products = Item::whereIn('id', ['ptr', 'pur'])->get();
            $response = 'Untuk rasa asin gurih khas ikan asin, kami rekomendasikan Peyek Teri dan Peyek Rebon. Keduanya memberikan citarasa asin gurih yang khas dari seafood berkualitas!';
        }
        // Cek rasa gurih kacang
        elseif ($this->containsAny($message, $kacang_keywords)) {
            $products = Item::whereIn('id', ['pkcg', 'pkdl', 'pkhj'])->get();
            $response = 'Untuk rasa gurih, kami punya Peyek Kacang Tanah, Peyek Kedelai, dan Peyek Kacang Hijau. Semuanya memberikan kelezatan kacang yang autentik dan gurih!';
        }
        // Keywords lainnya yang sederhana
        elseif (str_contains($message, 'harga') || str_contains($message, 'price') || str_contains($message, 'rp')) {
            $products = Item::all();
            $response = 'Berikut daftar harga peyek kami:
• Peyek Kacang: Rp50.000/kg
• Peyek Kedelai: Rp52.000/kg
• Peyek Kacang Hijau: Rp52.000/kg
• Peyek Teri: Rp56.000/kg
• Peyek Rebon: Rp60.000/kg';
        }
        elseif (str_contains($message, 'cara pesan') || str_contains($message, 'order') || str_contains($message, 'beli') || str_contains($message, 'pemesanan')) {
            $response = 'Cara pesan sangat mudah!
1) Pilih varian peyek yang diinginkan
2) Atur jumlah dalam kilogram
3) Klik tombol "Keranjang"
4) Isi data pengiriman
Tim kami akan segera memproses pesanan Anda!';
        }
        elseif (str_contains($message, 'info') || str_contains($message, 'produk') || str_contains($message, 'varian')) {
            $products = Item::all();
            $response = 'Kami menyediakan 5 varian peyek berkualitas:
• Peyek Kacang Tanah
• Peyek Kedelai
• Peyek Kacang Hijau
• Peyek Teri
• Peyek Rebon

Semua dibuat dengan bahan pilihan dan bumbu tradisional!';
        }
        elseif (str_contains($message, 'stok') || str_contains($message, 'available') || str_contains($message, 'tersedia')) {
            $availableProducts = Item::where('is_available', 1)->get();
            $products = $availableProducts;
            $response = 'Semua produk kami saat ini tersedia dan ready stock! Anda bisa langsung pesan varian yang diinginkan.';
        }
        // Pertanyaan kompleks akan dihandle oleh AI
        elseif ($this->isComplexQuestion($message)) {
            $handled = false;
        }
        else {
            $response = 'Maaf, saya belum memahami pertanyaan Anda. Anda bisa bertanya tentang: produk terlaris, rasa asin gurih, rasa gurih kacang, harga, cara pemesanan, atau info produk.';
        }

        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        // Format products jika ada
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

        return [
            'handled' => $handled,
            'message' => $response,
            'products' => $formatted_products,
            'response_time' => $responseTime
        ];
    }

    // METHOD: Deteksi pertanyaan kompleks yang butuh AI
    private function isComplexQuestion($message)
    {
        $complexKeywords = [
            // Pertanyaan analitis
            'berapa banyak', 'berapa lama', 'berapa rata', 'statistik', 'data penjualan', 'analisis',
            'trend', 'rekomendasi', 'saran', 'comparison', 'perbandingan',

            // Pertanyaan spesifik tentang data
            'bulan ini', 'minggu ini', 'tahun ini', 'periode', 'waktu penyelesaian',
            'rata-rata', 'persentase', 'prosentase',

            // Pertanyaan kondisional
            'jika', 'apabila', 'kalau', 'bagaimana jika', 'what if',

            // Pertanyaan tentang pengiriman kompleks
            'daerah mana', 'lokasi pengiriman', 'area jangkauan', 'jarak tempuh',

            // Pertanyaan tentang proses bisnis
            'proses pembuatan', 'bahan baku', 'cara membuat', 'resep',

            // Pertanyaan lain yang butuh analisis mendalam
            'kenapa', 'mengapa', 'alasan', 'sebab'
        ];

        return $this->containsAny($message, $complexKeywords);
    }

    // METHOD: Check if message contains any of the keywords
    private function containsAny($message, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }
        return false;
    }

    // METHOD BARU: Untuk debugging dan monitoring
    public function chatbotDebug(Request $request)
    {
        $message = $request->input('message');
        $ruleBasedCheck = $this->processRuleBased($message);
        $isComplex = $this->isComplexQuestion($message);

        return response()->json([
            'message' => $message,
            'rule_based_handled' => $ruleBasedCheck['handled'],
            'is_complex_question' => $isComplex,
            'rule_based_response' => $ruleBasedCheck['message'],
            'recommended_type' => $ruleBasedCheck['handled'] ? 'rule-based' : ($isComplex ? 'ai' : 'rule-based')
        ]);
    }

    // Method chatbot rule-based original (tetap ada untuk kompatibilitas)
    public function chatbot(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

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
        if ($this->containsAny($message, $terlaris_keywords)) {
            $products = Item::whereIn('id', ['pkdl', 'pkhj'])->get();
            $response = 'Produk terlaris kami adalah Peyek Kedelai dan Peyek Kacang Hijau! Kedua varian ini paling banyak dipesan karena citarasanya yang gurih dan berkualitas.';
        }
        // Cek rasa asin gurih
        elseif ($this->containsAny($message, $asin_keywords)) {
            $products = Item::whereIn('id', ['ptr', 'pur'])->get();
            $response = 'Untuk rasa asin gurih khas ikan asin, kami rekomendasikan Peyek Teri dan Peyek Rebon. Keduanya memberikan citarasa asin gurih yang khas dari seafood berkualitas!';
        }
        // Cek rasa gurih kacang
        elseif ($this->containsAny($message, $kacang_keywords)) {
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
