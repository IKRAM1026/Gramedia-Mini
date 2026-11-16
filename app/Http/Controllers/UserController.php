<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validateData = $request->validate([
            // "nama_input" => ['jenis_validate']
            // required : datanya harus diisi
            'name' => ['required', 'min:3'],
            // uniqe : email ga boleh duflikat : user,email : dari table user field email
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'max:10'],
        ]);

        // simpan data ke model user
        User::create([
            // nama field => Isian data
            'name' => $validateData['name'],
            'email' => $validateData['email'],
            // hashing : ubah plain text jadi karakter acak, digunakan pada data yang sifatnya rahasia
            'password' => Hash::make($validateData['password']),
            'role' => 'user',
        ]);

        // setelah data dibuat, halama akan di arahkan kemana
        // with(nama, 'pesan') : mengirim pesan notifikasi lx
        return redirect()->route('login')->with('success', 'Akun berhasil dibuat, silahkan login');

    }

    public function login(Request $request)
    {

        $validateData = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        // Auth::attempt : memerlukan data username/email dan pw yang sudah di hashing
        // yng di lakukannya
        // 1. cek pw yng di hashing apakaah sma kaya pw text yang di input
        // 2. mencocockan username/email dan pw tsb
        // 3. kalau cocok data penggunannya disimpan di session/cookies web

        $data = [
            'email' => $validateData['email'],
            'password' => $validateData['password'],
        ];

        if (Auth::attempt($data)) {

            if (Auth::user()->role == 'admin') {

                return redirect()->route('admin.dashboard')->with('success', 'login berhasil');
            } else {
                return redirect()->route('home')->with('success', 'login berhasil');
            }

        } else {
            return redirect()->route('login')->with('error', 'email dan password tidak sesuai silahkan coba lagi');
        }

    }

    public function logout()
    {
        // hapus sesi login
        Auth::logout();

        // setelah di logout arahkan ke halaman nama
        return redirect()->route('home')->with('success', 'Anda telah logout');
    }

    public function account()
    {
        $user = Auth::user()->load([
            'subscriptPackageUser.subscriptionPackage',
            'checkout.checkoutbook.book',
        ]);
        $type = request()->string('type')->toString() === 'book' ? 'book' : 'subscription';

        return view('account', [
            'user' => $user,
            'subscriptionHistories' => $user->subscriptPackageUser()->with('subscriptionPackage')->latest()->get(),
            'bookHistories' => $user->checkout()->with('checkoutbook.book')->latest()->get(),
            'type' => $type,
        ]);
    }
}
