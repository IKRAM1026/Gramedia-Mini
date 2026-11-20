<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            // idi input name="selected_carts" harus array dengan min 1 item yang dipilih
            'selected_carts' => ['required', 'array', 'min:1'],     
            'selected_carts.*' => ['exists:carts,id'],  // isi array berupa id dari data table carts
        ]);

        
        // WhereIN : mencari data dari array, where : nyari data selain array (string/int/lainnya)
        $cartItems = Cart::whereIn('Id', $request->selected_carts)->where('user_id', Auth::user()->id)->with
        (['book', 'subscriptionPackage'])->get();

        // membuat data harga dari item keranjang (harga buku/harga paket langganan yang dibelinya)
        $prices = $cartItems->mapWithKeys(fn ($row) => [
            // hasil array nya ['id' => 'harga'] : ['1' => 90000] : item keranjang no 1 harganya 90000
            $row->id => $row->subscription_package_id == NULL ? $row->book->price : 
            $row->subscriptionPackage->price 
        ]);
        $totalPrice = $prices->sum();
        dd($totalPrice);
    }

    /**
     * Display the specified resource.
     */
    public function show(Checkout $checkout)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Checkout $checkout)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Checkout $checkout)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Checkout $checkout)
    {
        //
    }
}