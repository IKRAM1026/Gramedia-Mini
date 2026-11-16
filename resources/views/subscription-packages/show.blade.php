@extends('layout.app')

@push('styles')
    <style>
        .package-header {
            padding-top: 8px;
        }

        .package-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
        }

        .package-title-row h1 {
            margin-bottom: 6px;
            color: #334155;
            font-size: 1.15rem;
            font-weight: 700;
        }

        .package-price {
            margin-bottom: 0;
            color: #334155;
            font-size: 0.9rem;
        }

        .package-price strong {
            color: #16a34a;
        }

        .package-subscribe-button {
            flex: 0 0 auto;
            border: 0;
            border-radius: 5px;
            background: #0d6efd;
            color: #fff;
            padding: 9px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(15, 23, 42, 0.12);
        }

        .package-search-form {
            max-width: 620px;
            margin: 30px 0 22px;
        }

        .package-search-form .input-group {
            border: 1px solid #dee2e6;
            border-radius: 999px;
            background: #fff;
            overflow: hidden;
        }

        .package-search-form input {
            border: 0;
            border-radius: 999px;
            padding: 11px 18px;
            box-shadow: none;
        }

        .package-search-form input:focus {
            box-shadow: none;
        }

        .package-search-form button {
            border: 0;
            border-radius: 0 999px 999px 0;
            background: transparent;
            color: #94a3b8;
        }

        .package-book-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 16px;
        }

        .package-book-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 2px 7px rgba(15, 23, 42, 0.04);
        }

        .package-book-card img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            display: block;
        }

        .package-book-card .package-book-body {
            padding: 12px;
        }

        .package-book-card h5 {
            margin-bottom: 8px;
            color: #334155;
            font-size: 0.9rem;
            font-weight: 600;
            white-space: normal;
        }

        .package-book-tag {
            margin-bottom: 7px;
            color: #64748b;
            font-size: 0.72rem;
        }

        @media (max-width: 1200px) {
            .package-book-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        }

        @media (max-width: 992px) {
            .package-book-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-width: 576px) {
            .package-book-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .package-title-row {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')
<div class="container py-4">
    <div class="package-header">
        <div class="package-title-row">
            <div>
                <h1>Paket Langganan: {{ $subscriptionPackage->nama_package }}</h1>
                <p class="package-price">
                    Harga: <strong>Rp {{ number_format((float) $subscriptionPackage->price, 0, ',', '.') }}</strong>
                </p>
            </div>
            @auth
                <a href="{{ route('subscription-packages.checkout.page', $subscriptionPackage) }}" class="package-subscribe-button text-decoration-none">Langganan Sekarang</a>
            @else
                <a href="{{ route('login') }}" class="package-subscribe-button text-decoration-none">Langganan Sekarang</a>
            @endauth
        </div>

        <form method="GET" action="{{ route('subscription-packages.show', $subscriptionPackage) }}" class="d-flex gap-2">
            <div class="input-group package-search-form">
                <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Cari judul, produk, penulis..." aria-label="Cari buku atau penulis">
                <button type="submit" aria-label="Cari">
                    <i class="fa-solid fa-magnifying-glass px-3"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="package-book-grid">
        @forelse ($books as $book)
            <article class="package-book-card">
                <img src="{{ asset($book->cover) }}" alt="{{ $book->title }}">
                <div class="package-book-body">
                    <div class="package-book-tag">
                        <i class="fa-solid fa-mobile me-1"></i> PDF
                    </div>
                    <div class="text-secondary small mb-1">{{ $book->writer }}</div>
                    <h5>{{ $book->title }}</h5>
                    <a href="{{ route('books.checkout.page', $book) }}" class="btn btn-primary btn-sm w-100 mt-1">
                        Rp {{ number_format((float) $book->price, 0, ',', '.') }}
                    </a>
                </div>
            </article>
        @empty
            <p class="text-secondary">Belum ada buku yang sesuai dengan paket ini.</p>
        @endforelse
    </div>
</div>
@endsection
