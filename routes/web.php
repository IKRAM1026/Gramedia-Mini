<?php

use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubscriptionPackageController;
use App\Http\Controllers\Usercontroller;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

// Route::get('/', function () {
//     $subscriptionPackages = SubscriptionPackage::all();

//     return view('home', compact('subscriptionPackages'));
// })-> name('home');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kategori-buku/{bookCategory}', [HomeController::class, 'category'])->name('books.category');
Route::get('/buku/{book}', [BookController::class, 'show'])->name('books.show');
Route::get('/paket-langganan/{subscriptionPackage}', [HomeController::class, 'subscriptionPackage'])->name('subscription-packages.show');

// kelompok route yang gaboleh di akses kalau uda login
Route::middleware(['IsGuest'])->group(function () {
    Route::get('/register', function () {
        return view('register');
    })->name('register');

    // untuk menyimpan daata akun register
    Route::POST('/register', [Usercontroller::class, 'store',
    ])->name('register.store');

    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::POST('/login', [Usercontroller::class, 'login',
    ])->name('login.store');
});

// kelompok route yang boleh di akses nya hanya setelah melakukan login
Route::middleware(['IsLoggedIn'])->group(function () {
    Route::get('/logout', [Usercontroller::class, 'logout'])->name('logout');
    Route::get('/akun', [Usercontroller::class, 'account'])->name('account');

    Route::get('/paket-langganan/{subscriptionPackage}/checkout', [SubscriptionPackageController::class, 'checkoutPage'])->name('subscription-packages.checkout.page');
    Route::post('/paket-langganan/{subscriptionPackage}/checkout', [SubscriptionPackageController::class, 'checkout'])->name('subscription-packages.checkout');
    
    Route::get('/buku/{book}/checkout', [HomeController::class, 'bookCheckoutPage'])->name('books.checkout.page');
    Route::post('/buku/{book}/checkout', [HomeController::class, 'bookCheckout'])->name('books.checkout');

    Route::get('/carts', [CartController::class, 'index'])->name('carts');
    Route::post('/carts/add', [CartController::class, 'store'])->name('carts.store');
    Route::delete('/carts/{id}', [CartController::class, 'destroy'])->name('carts.destroy');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

Route::middleware(['IsAdmin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/book-categories/export', [BookCategoryController::class, 'export'])->name('book-categories.export');
        // Route::resource artnya sudah menyediakan semua method yang di perlukan (CRUD)
        Route::resource('book-categories', BookCategoryController::class);
        Route::get('/subscription-packages/export', [SubscriptionPackageController::class, 'export'])->name('subscription-packages.export');
        Route::resource('subscription-packages', SubscriptionPackageController::class)->except(['show']);
        Route::get('/subscription-packages/{subscriptionPackage}/books', [SubscriptionPackageController::class, 'books'])->name('subscription-packages.books.index');
        Route::get('/subscription-packages/{subscriptionPackage}/books/add', [SubscriptionPackageController::class, 'addBooks'])->name('subscription-packages.books.add');
        Route::post('/subscription-packages/{subscriptionPackage}/books', [SubscriptionPackageController::class, 'attachBooks'])->name('subscription-packages.books.attach');
        Route::get('/books/export', [BookController::class, 'export'])->name('books.export');
        Route::resource('books', BookController::class)->except(['show']);

    });

});
