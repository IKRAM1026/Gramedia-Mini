@extends('layout.app')

@push('styles')
    <style>
        .category-book-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 16px;
        }

        .category-book-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
        }

        .category-book-card img {
            width: 100%;
            height: 260px;
            object-fit: cover;
            display: block;
        }

        @media (max-width: 992px) {
            .category-book-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-width: 576px) {
            .category-book-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('home') }}" class="text-secondary text-decoration-none">Beranda</a>
            <h1 class="mt-2 mb-0 h3 text-dark">{{ $bookCategory->name }}</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="{{ route('books.category', $bookCategory) }}" class="d-flex align-items-center gap-2">
                @if ($sort)
                    <input type="hidden" name="sort" value="{{ $sort }}">
                @endif
                <input type="search"
                    name="search"
                    value="{{ $search }}"
                    class="form-control"
                    placeholder="Cari judul atau penulis"
                    aria-label="Cari judul atau penulis">
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
            <form method="GET" action="{{ route('books.category', $bookCategory) }}">
                @if ($search)
                    <input type="hidden" name="search" value="{{ $search }}">
                @endif
                <select name="sort" class="form-select" onchange="this.form.submit()" aria-label="Urutkan buku">
                    <option value="latest" {{ ($sort ?: 'latest') === 'latest' ? 'selected' : '' }}>Buku terbaru</option>
                    <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Harga termurah</option>
                    <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Harga termahal</option>
                </select>
            </form>
        </div>
    </div>

    <div class="category-book-grid">
        @forelse ($books as $book)
            <article class="category-book-card">
                <img src="{{ asset($book->cover) }}">
                <div class="p-3">
                    
                    <h5 class="fw-bold mb-2 ">{{ $book->title }}</h5>
                    <div class="text-secondary small">{{ $book->publisher }}</div>
                    <div class="text-secondary small mb-2">Penulis: {{ $book->writer }}</div>
                    <div class="fw-bold text-dark">Rp {{ number_format((float) $book->price, 0, ',', '.') }}</div>
                </div>
            </article>
        @empty
            <p class="text-secondary">Belum ada buku pada kategori ini.</p>
        @endforelse
    </div>
</div>
@endsection
