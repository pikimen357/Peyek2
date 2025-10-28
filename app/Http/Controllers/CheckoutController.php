<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Http\Request;
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
            $price = $prices->get($item); // coba coba
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


    }

}
