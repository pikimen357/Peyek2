<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(){
        $items = Item::all();

        return view('customer.products', [
            'items' => $items,
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
}
