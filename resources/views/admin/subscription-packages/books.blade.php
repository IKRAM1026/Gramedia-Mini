@extends('layout.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('admin.subscription-packages.index') }}" class="text-secondary text-decoration-none">Paket Langganan</a>
            <h1 class="h3 mt-2 mb-0">Buku dalam Paket: {{ $subscriptionPackage->nama_package }}</h1>
        </div>
        <a href="{{ route('admin.subscription-packages.books.add', $subscriptionPackage) }}" class="btn btn-primary">Tambah Buku</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar Buku</span>
            <form method="GET" action="{{ route('admin.subscription-packages.books.index', $subscriptionPackage) }}" class="d-flex gap-2">
                <input type="search" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search" aria-label="Cari buku dalam paket">
                <button type="submit" class="btn btn-sm btn-outline-primary">Cari</button>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th style="width: 70px;">No</th>
                        <th>Judul Buku</th>
                        <th>Kategori</th>
                        <th style="width: 90px;">Cover</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($books as $index => $book)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $book->id }}</td>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->bookCategory->name ?? '-' }}</td>
                            <td><img src="{{ asset($book->cover) }}" alt="{{ $book->title }}" style="width: 45px; height: 55px; object-fit: cover;"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-4">Belum ada buku dalam paket ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
