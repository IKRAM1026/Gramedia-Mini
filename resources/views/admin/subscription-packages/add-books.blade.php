@extends('layout.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('admin.subscription-packages.books.index', $subscriptionPackage) }}" class="text-secondary text-decoration-none">Buku dalam Paket</a>
            <h1 class="h3 mt-2 mb-0">Tambah Buku ke Paket: {{ $subscriptionPackage->nama_package }}</h1>
        </div>
        <a href="{{ route('admin.subscription-packages.books.index', $subscriptionPackage) }}" class="btn btn-secondary">Kembali</a>
    </div>

    <form method="GET" action="{{ route('admin.subscription-packages.books.add', $subscriptionPackage) }}" class="mb-3">
        <div class="input-group">
            <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Cari judul, penulis, atau penerbit" aria-label="Cari buku">
            <button type="submit" class="btn btn-outline-primary">Cari</button>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.subscription-packages.books.attach', $subscriptionPackage) }}">
        @csrf
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Pilih Buku yang Belum Ditambahkan</span>
                <button type="submit" class="btn btn-sm btn-primary">+ Tambah ke Paket</button>
            </div>
            <div class="card-body p-0">
                @error('book_ids')
                    <div class="alert alert-danger m-3">{{ $message }}</div>
                @enderror
                <table class="table table-bordered mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Pilih</th>
                            <th style="width: 70px;">ID</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th style="width: 90px;">Cover</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($books as $book)
                            <tr>
                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox" name="book_ids[]" value="{{ $book->id }}" id="book-{{ $book->id }}">
                                </td>
                                <td>{{ $book->id }}</td>
                                <td><label for="book-{{ $book->id }}">{{ $book->title }}</label></td>
                                <td>{{ $book->writer }}</td>
                                <td>{{ $book->bookCategory->name ?? '-' }}</td>
                                <td><img src="{{ asset($book->cover) }}" alt="{{ $book->title }}" style="width: 45px; height: 55px; object-fit: cover;"></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">Tidak ada buku lain yang dapat ditambahkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
@endsection
