<?php
// app/Services/DeepSeekService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\DB;

class DeepSeekService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('DEEPSEEK_API_KEY');
        $this->baseUrl = env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com/v1');
    }

    public function handleCustomerQuestion($userQuestion)
    {
        // Ambil data konteks dari database
        $context = $this->getDatabaseContext();

        $prompt = $this->buildCustomerServicePrompt($userQuestion, $context);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Anda adalah customer service untuk bisnis Peyek. Berikan jawaban yang ramah, informatif, dan membantu berdasarkan data yang tersedia.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['choices'][0]['message']['content'])) {
                    return $this->cleanResponse($data['choices'][0]['message']['content']);
                } else {
                    throw new Exception('Invalid API response format');
                }

            } else {
                $error = $response->json();
                throw new Exception('API Error: ' . ($error['error']['message'] ?? $response->body()));
            }

        } catch (Exception $e) {
            throw new Exception('DeepSeek Service Error: ' . $e->getMessage());
        }
    }

    private function getDatabaseContext()
    {
        $context = [];

        // Data produk peyek
        $context['products'] = DB::table('items')
            ->where('is_available', 1)
            ->select('id', 'nama_peyek', 'topping', 'hrg_kiloan', 'deskripsi')
            ->get()
            ->toArray();

        // Data lokasi pengiriman
        $context['locations'] = DB::table('locations')
            ->select('desa', 'kecamatan', 'jarak')
            ->get()
            ->toArray();

        // Data statistik penjualan dari order_items (LEBIH AKURAT!)
        $context['sales_stats'] = $this->getSalesStatistics();

        // Data statistik pesanan
        $context['order_stats'] = [
            'total_orders' => DB::table('orders')->count(),
            'completed_orders' => DB::table('orders')->where('status', 'selesai')->count(),
            'avg_preparation_time' => $this->calculateAvgPreparationTime(),
        ];

        return $context;
    }

    private function getSalesStatistics()
    {
        // Analisis produk terlaris berdasarkan order_items
        $bestSellers = DB::table('order_items')
            ->join('items', 'order_items.item_id', '=', 'items.id')
            ->select(
                'items.nama_peyek',
                'items.topping',
                DB::raw('SUM(order_items.jumlah_kg) as total_kg_terjual'),
                DB::raw('SUM(order_items.total_harga) as total_pendapatan'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as jumlah_transaksi')
            )
            ->groupBy('items.id', 'items.nama_peyek', 'items.topping')
            ->orderByDesc('total_kg_terjual')
            ->limit(5)
            ->get();

        // Total penjualan semua produk
        $totalSales = DB::table('order_items')
            ->select(
                DB::raw('SUM(jumlah_kg) as total_kg_all'),
                DB::raw('SUM(total_harga) as total_revenue_all')
            )
            ->first();

        // Penjualan hari ini
        $todaySales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereDate('orders.created_at', today())
            ->select(
                DB::raw('SUM(order_items.jumlah_kg) as kg_hari_ini'),
                DB::raw('SUM(order_items.total_harga) as revenue_hari_ini')
            )
            ->first();

        return [
            'best_sellers' => $bestSellers,
            'total_sales' => $totalSales,
            'today_sales' => $todaySales,
            'avg_order_value' => $totalSales->total_revenue_all / max(1, DB::table('orders')->count())
        ];
    }

    private function buildCustomerServicePrompt($question, $context)
    {
        $productsText = "";
        foreach ($context['products'] as $product) {
            $productsText .= "• {$product->nama_peyek}" .
                           ($product->topping ? " + {$product->topping}" : "") .
                           " - Rp " . number_format($product->hrg_kiloan) . "/kg\n";
        }

        $locationsText = "";
        foreach ($context['locations'] as $location) {
            $locationsText .= "• {$location->desa}, {$location->kecamatan} (jarak: {$location->jarak}km)\n";
        }

        // Best sellers information
        $bestSellersText = "";
        foreach ($context['sales_stats']['best_sellers'] as $index => $product) {
            $bestSellersText .= ($index + 1) . ". {$product->nama_peyek}" .
                              ($product->topping ? " + {$product->topping}" : "") .
                              " - {$product->total_kg_terjual} kg terjual\n";
        }

        return "
        Anda adalah customer service untuk bisnis Peyek. Berikan jawaban dalam Bahasa Indonesia yang ramah dan informatif.

        INFORMASI TOKO:

        PRODUK YANG TERSEDIA:
        {$productsText}

        PRODUK TERLARIS (berdasarkan data penjualan):
        {$bestSellersText}

        LOKASI PENGIRIMAN:
        {$locationsText}

        STATISTIK PENJUALAN:
        - Total penjualan: " . number_format($context['sales_stats']['total_sales']->total_kg_all ?? 0) . " kg
        - Penjualan hari ini: " . number_format($context['sales_stats']['today_sales']->kg_hari_ini ?? 0) . " kg
        - Rata-rata nilai pesanan: Rp " . number_format($context['sales_stats']['avg_order_value'] ?? 0) . "
        - Rata-rata waktu penyelesaian pesanan: {$context['order_stats']['avg_preparation_time']}

        PERTANYAAN PELANGGAN: \"{$question}\"

        INSTRUKSI:
        1. Jawab dengan ramah dan membantu
        2. Gunakan informasi di atas untuk menjawab pertanyaan
        3. Untuk pertanyaan produk terlaris, gunakan data aktual dari 'PRODUK TERLARIS'
        4. Jika tidak tahu, jangan mengarang jawaban
        5. Untuk pertanyaan tentang pengiriman, jelaskan daerah yang kami layani
        6. Format jawaban dengan rapi dan mudah dibaca
        7. Berikan rekomendasi berdasarkan data penjualan jika relevan

        JAWABAN:
        ";
    }

    private function calculateAvgPreparationTime()
    {
        $orders = DB::table('orders')
            ->where('status', 'selesai')
            ->whereNotNull('tanggal_selesai')
            ->select('created_at', 'tanggal_selesai')
            ->get();

        if ($orders->isEmpty()) return "1-2 jam";

        $totalHours = 0;
        foreach ($orders as $order) {
            $start = \Carbon\Carbon::parse($order->created_at);
            $end = \Carbon\Carbon::parse($order->tanggal_selesai);
            $totalHours += $end->diffInHours($start);
        }

        $avgHours = $totalHours / count($orders);
        return round($avgHours, 1) . " jam";
    }

    private function cleanResponse($response)
    {
        return trim($response);
    }

    // Method khusus untuk analisis data (bisa digunakan untuk dashboard admin nanti)
    public function getProductAnalytics()
    {
        return DB::table('order_items')
            ->join('items', 'order_items.item_id', '=', 'items.id')
            ->select(
                'items.nama_peyek',
                'items.topping',
                DB::raw('SUM(order_items.jumlah_kg) as total_kg'),
                DB::raw('SUM(order_items.total_harga) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
                DB::raw('AVG(order_items.jumlah_kg) as avg_kg_per_order')
            )
            ->groupBy('items.id', 'items.nama_peyek', 'items.topping')
            ->orderByDesc('total_kg')
            ->get();
    }
}
