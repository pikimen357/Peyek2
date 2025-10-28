<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function index(){
        $items = Session::get('cart');
        if(empty($items)){
            return redirect()->route('cart.show')
                ->with('error', 'Your cart is empty!');
        }
        return view('customer.checkout', compact('items'));
    }
}
