<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\SubscriptionPackage;
use App\Models\SubscriptionPackageUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Mengecek apakah request yang masuk merupakan request AJAX. Kalau iya, controller akan mengirim data untuk DataTables.
            $model = SubscriptionPackage::query();

            return DataTables::eloquent($model)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.subscription-packages.edit', $row->id);
                    $destroyUrl = route('admin.subscription-packages.destroy', $row->id);
                    $csrf = csrf_field();
                    // menghasilkan token untuk perlindungan CSRF.
                    $method = method_field('DELETE');

                    return '
                        <a href="'.route('admin.subscription-packages.books.index', $row).'" class="btn btn-sm btn-info text-white">Buku</a>
                        <a href="'.$editUrl.'" class="btn btn---sm btn-warning">Edit</a>
                        <form action="'.$destroyUrl.'" method="POST" class="d-inline">
                            '.$csrf.'
                            '.$method.'
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm(\'Yakin ingin menghapus paket ini?\')">
                                Hapus
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['action'])
                // biar emang keliatan tombolnya asli ga htmlnya doang

                ->toJson();
            //  Mengubah data tabel menjadi format JSON supaya bisa dibaca JavaScript.

        }

        return view('admin.subscription-packages.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subscription-packages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama_package' => ['required', 'string', 'max:225'],
            'description' => ['required', 'string', 'max:500'],
            'color' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
        ]);
        SubscriptionPackage::create($validateData);

        return redirect()->route('admin.subscription-packages.index')->with('success', 'Paket langganan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubscriptionPackage $subscriptionPackage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $subscriptionPackage = SubscriptionPackage::findOrFail($id);

        return view('admin.subscription-packages.edit', compact('subscriptionPackage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $subscriptionPackage = SubscriptionPackage::findOrFail($id);

        $validateData = $request->validate([
            'nama_package' => ['required', 'string', 'max:225'],
            'description' => ['required', 'string', 'max:225'],
            'color' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        $subscriptionPackage->update($validateData);

        return redirect()->route('admin.subscription-packages.index')->with('success', 'Paket langganan berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $subscriptionPackage = SubscriptionPackage::findOrFail($id);
        $subscriptionPackage->delete();

        return redirect()->route('admin.subscription-packages.index');
    }

    public function books(Request $request, SubscriptionPackage $subscriptionPackage)
    {
        $search = $request->string('search')->trim()->toString();
        $books = $subscriptionPackage->books()->with('bookCategory')->latest();

        if ($search !== '') {
            $books->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('writer', 'like', '%'.$search.'%');
            });
        }

        return view('admin.subscription-packages.books', [
            'subscriptionPackage' => $subscriptionPackage,
            'books' => $books->get(),
            'search' => $search,
        ]);
    }

    public function addBooks(Request $request, SubscriptionPackage $subscriptionPackage)
    {
        $search = $request->string('search')->trim()->toString();
        $books = Book::query()
            ->whereDoesntHave('subscriptionPackages', function ($query) use ($subscriptionPackage) {
                $query->whereKey($subscriptionPackage->id);
            })
            ->latest();

        if ($search !== '') {
            $books->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('writer', 'like', '%'.$search.'%')
                    ->orWhere('publisher', 'like', '%'.$search.'%');
            });
        }

        return view('admin.subscription-packages.add-books', [
            'subscriptionPackage' => $subscriptionPackage,
            'books' => $books->get(),
            'search' => $search,
        ]);
    }

    public function attachBooks(Request $request, SubscriptionPackage $subscriptionPackage)
    {
        $validated = $request->validate([
            'book_ids' => ['required', 'array', 'min:1'],
            'book_ids.*' => ['integer', 'distinct', 'exists:books,id'],
        ]);

        $availableBookIds = Book::query()
            ->whereKey($validated['book_ids'])
            ->whereDoesntHave('subscriptionPackages', function ($query) use ($subscriptionPackage) {
                $query->whereKey($subscriptionPackage->id);
            })
            ->pluck('id');

        $subscriptionPackage->books()->syncWithoutDetaching($availableBookIds);

        return redirect()
            ->route('admin.subscription-packages.books.index', $subscriptionPackage)
            ->with('success', 'Buku berhasil ditambahkan ke paket langganan.');
    }

    public function checkoutPage(SubscriptionPackage $subscriptionPackage)
    {
        $qrCode = $this->generateQrCode('SUBSCRIPTION-'.$subscriptionPackage->id.'-USER-'.Auth::id());

        return view('user.subscription-packages.checkout', [
            'subscriptionPackage' => $subscriptionPackage,
            'qrCode' => $qrCode,
        ]);
    }

    public function checkout(SubscriptionPackage $subscriptionPackage)
    {
        DB::transaction(function () use ($subscriptionPackage) {
            SubscriptionPackageUser::create([
                'subscription_package_id' => $subscriptionPackage->id,
                'user_id' => Auth::id(),
                'expired_date' => now()->addDays(30)->toDateString(),
            ]);
        });

        return redirect()->route('account')->with('success', 'Pembayaran paket langganan berhasil.');
    }

    private function generateQrCode(string $code): string
    {
        return (string) QrCode::format('svg')->size(250)->generate($code);
    }
}
