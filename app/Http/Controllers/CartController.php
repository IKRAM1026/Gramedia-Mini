<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscriptionPackageUser;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        //ambil keragjang  milik pengguna yang login
        $userid = Auth::user()->id;
        $carts = Cart::where('user_id', $userid)->get();
        // dd($carts->toArray());

        //paket langganan aktif milik pengguna yang login
        $userid = Auth::user()->id;
        $activePackageId = SubscriptionPackageUser::where('user_id', $userid)
            ->whereDate('expired_date', '>', today())
            ->pluck('subscription_package_id');


        $packageUserNotBought = $carts
            ->whereNotNull('subscription_package_id')//jangan ambil yang subscription_package_id nya null(buku satuan)
            ->pluck('subscription_package_id')
            ->unique()//subscription_package_id diambil unik( tidak duplikat)
            ->diff($activePackageId)//membandingkan subscription_package_id yang belum ada di $activePackageId (paket yang udah dibeli)
            ->values(); //ambil ulang colection yang sudah di sesuaikan (filter)

            $carts = $carts->each(function ($cart) use ($packageUserNotBought) {
                $cart->display_price = $cart->subscriptionPackage ? $cart->subscriptionPackage->price : $cart->book->price;
                $cart->needs_package = $packageUserNotBought->contains($cart->subscription_package_id);
            });

            $totalPrice = $carts->sum('display_price');

        return view('user.cart.index', compact('carts', 'totalPrice'));
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
        //cek data 
        $request->validate([
            'purchase_option' => 'required',
            'book_id' => 'required',
        ]);

        if( $request->action == "add_to_cart" ){
            Cart::create([
                'user_id' => Auth::user()->id,
                'book_id' => $request->book_id,
                // Jika purchase optionnya none (milih satuan), maka paket langganan di kosongkan (NULL)
                'subscription_package_id' => $request->purchase_option === 'none' ? null : $request->purchase_option,
            ]);
            return redirect()->route('carts')->with('success', 'Buku berhasil ditambahkan ke keranjang.');
        }else {
            //untuk proses checkout
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Cart::where('id', $id)->delete();
        return redirect()->route('carts')->with('success', 'Buku berhasil dihapus dari keranjang.');
    }
}
