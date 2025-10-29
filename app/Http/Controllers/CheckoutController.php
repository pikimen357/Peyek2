<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Item;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function index(){
        $items = Session::get('cart');
        $locations = Location::select('desa', 'kecamatan')->get();

        if(empty($items)){
            return redirect()->route('cart.show')
                ->with('error', 'Your cart is empty!');
        }
        return view('customer.checkout',
            compact(['items', 'locations']));
    }

    /**
     * Method untuk mendapatkan id_lokasi berdasarkan kecamatan dan desa
     */
    private function getLocationId($kecamatan, $desa)
    {
        $location = Location::where('kecamatan', $kecamatan)
                           ->where('desa', $desa)
                           ->first();

        return $location ? $location->id : null;
    }

    public function store(CheckoutRequest $request){
        $cart = Session::get('cart');

        if(empty($cart)){
            return redirect()->route('cart.show')
                ->with('error', 'Keranjang kosong');
        }

        $itemIds = collect($cart)->pluck('id');

        // Retrieve all prices from the database in a single query
        $prices = Item::whereIn('id', $itemIds)
            ->where('is_active', true)
            ->pluck('hrg_kiloan', 'id'); // [id => hrg_kiloan]

        // Calculate the total and prepare the itemDetails
        $totalAmount = 0;
        $itemDetails = [];

        foreach($cart as $item){
            $price = $prices->get($item['id']);; // coba coba
            $subtotal = $price * $item['qty'];
            $totalAmount += $subtotal;

            $itemDetails[] = [
                'id' => $item['id'],
                'price' => (int) $price,
                'qty' => $item['qty'],
                'nama_peyek' => substr($item['nama'], 0, 50),
            ];
        }

        // save order
        $validated_data = $request->validated();

        $user = User::updateOrCreate(
            [
                'telepon' => $validated_data['telepon']
            ],
            [
                'nama' => $validated_data['nama'],
                'id_lokasi' => $this->getLocationId($validated_data['kecamatan'], $validated_data['desa']),
                'alamat' => $validated_data['alamat'] ?? null, // Opsional, pakai null coalescing
            ]
        );

        $locationId = $this->getLocationId($validated_data['kecamatan'], $validated_data['desa']);

        $jarakLocation = Location::findOrFail($locationId, ['jarak']);

        // Ongkir dihitung dari 2000 dikalikan jaraknya
        $ongkir = $jarakLocation->jarak * 2000;

        // Selanjutnya Simpan Order
        $order = Order::create([
            'user_id' => $user->id,
            'location_id' => $locationId,
            'no_order' => 'ORD-' . $user->nama . '-' . time(),
            'status' => 'belum bayar',
            'payment_method' => $validated_data['payment_method'],
            'catatan' => $validated_data['catatan'],
            'detail_alamat' => $validated_data['alamat'],
            'ongkir' => $ongkir,
            'subtotal' => $totalAmount,
            // tanggal selesai diurus nanti
        ]);

        // Simpan ke order item
        foreach ($cart as $item){
            $hrg_kiloan = $prices->get($item['id']); // coba coba ($price = $prices[$item['id']];)
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $item['id'],
                'jumlah_kg' => $item['berat_kg'],
                'harga_per_kg' => $hrg_kiloan,
                'total_harga' => $hrg_kiloan * $item['berat_kg'],
            ]);
        }

        Session::forget('cart');

//        if ($validated_data['payment_method'] == 'cash'){
//
//             return redirect()->route('checkout.success', ['orderCode' => $order->order_code])
//                ->with('success', 'Pesanan berhasil dibuat');
//         } else {
//            return redirect()->route('checkout.success', ['orderCode' => $order->order_code]);
//        }

        if ($validated_data['payment_method'] == 'cash'){
             return redirect()->route('landing')
                ->with('success', 'Pesanan berhasil dibuat (cash)');
         } else {

            //MIDTRANS INTEGRATION (next)

            return redirect()->route('landing')
                ->with('success', 'Pesanan berhasil dibuat (non tunai)');
        }

    }

}
