<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Checkout;
use App\Models\CheckoutBook;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HomeController extends Controller
{
    public function index()
    {
        $subscriptionPackages = SubscriptionPackage::latest()->take(3)->get();
        $freeBooks = Book::where('price', 0)->latest()->limit(6)->get();
        $latestBooks = Book::with('bookCategory')->latest()->limit(10)->get();

        return view('home', compact(
            'subscriptionPackages',
            'latestBooks',
            'freeBooks',
        ));
    }

    public function category(Request $request, BookCategory $bookCategory)
    {
        $books = $bookCategory->book()->with('bookCategory');

        $search = $request->string('search')->trim()->toString();
        if ($search !== '') {
            $books->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%');
            });
        }

        match ($request->string('sort')->toString()) {
            'price_asc' => $books->orderBy('price'),
            'price_desc' => $books->orderByDesc('price'),
            default => $books->latest(),
        };

        return view('books.category', [
            'bookCategory' => $bookCategory,
            'books' => $books->get(),
            'sort' => $request->string('sort')->toString(),
            'search' => $search,
        ]);
    }

    public function subscriptionPackage(Request $request, SubscriptionPackage $subscriptionPackage)
    {
        $search = $request->string('search')->trim()->toString();
        $books = $subscriptionPackage->books()->with('bookCategory');

        if ($search !== '') {
            $books->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('writer', 'like', '%'.$search.'%');
            });
        }

        return view('subscription-packages.show', [
            'subscriptionPackage' => $subscriptionPackage,
            'books' => $books->latest()->get(),
            'search' => $search,
        ]);
    }

    public function bookCheckoutPage(Book $book)
    {
        $qrCode = (string) QrCode::format('svg')->size(250)->generate('BOOK-'.$book->id.'-USER-'.Auth::id());

        return view('user.books.checkout', [
            'book' => $book,
            'qrCode' => $qrCode,
        ]);
    }

    public function bookCheckout(Book $book)
    {
        DB::transaction(function () use ($book) {
            $checkout = Checkout::create([
                'user_id' => Auth::id(),
                'payment-method' => 'Qris',
                'total_book' => 1,
                'total_book_price' => (int) $book->price,
                'total_price' => (int) $book->price,
            ]);

            CheckoutBook::create([
                'checkout_id' => $checkout->id,
                'book_id' => $book->id,
                'total_book' => 1,
            ]);
        });

        return redirect()->route('account')->with('success', 'Pembayaran buku berhasil.');
    }
}
