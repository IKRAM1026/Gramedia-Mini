@extends('layout.app')

@push('styles')
<style>
    .book-detail-cover {
        min-height: 380px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .book-detail-cover img {
        max-height: 380px;
        height: 33vh;
        width: 260px;
        max-width: 100%;
        object-fit: contain;
    }

    .style-width-label {
        min-width: 100px;
        display: inline-block;
    }

    .description-clamped {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .style-width-label-info {
        min-width: 100px;
        display: inline-block;
    }

    .fixed-bottom-detail {
        z-index: 1020;
    }

    .purchase-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, 170px);
        gap: 12px;
    }

    .purchase-options .form-selectgroup-item {
        width: 170px;
    }

    .purchase-option-card {
        min-height: 108px;
    }

    @media (max-width: 576px) {
        .purchase-options {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .purchase-options .form-selectgroup-item {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<form action="{{  route('carts.store') }}" method="POST">
    @csrf
    <input type="hidden" name="book_id" value="{{ $book->id }}">
<div class="container-xl px-5 py-4 bg-white">
    <div class="row g-4">
        <div class="col-lg-5 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="text-center rounded bg-light p-4 mb-3">
                    <div class="book-detail-cover">
                        <img src="{{ asset($book->cover) }}" alt="{{ $book->title }}" class="img-fluid rounded shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 col-md-6">
            <h1 class="h2 fw-bold text-dark mb-0">{{ $book->title }}</h1>

           <div class="mb-4">
    <label class="form-label fw-bold text-muted small text-uppercase mb-2">Opsi Pembelian</label>
    <div class="purchase-options">
        {{-- 1. Opsi Beli Satuan (Selalu tampil & otomatis terpilih pertama kali) --}}
        <label class="form-selectgroup-item">
            <input type="radio" name="purchase_option" value="none" class="form-selectgroup-input" checked>
            <span class="form-selectgroup-label purchase-option-card d-flex flex-column align-items-start justify-content-start p-3 border rounded">
                <span class="text-muted small">Satuan</span>
                <span class="h3 fw-bold text-dark mb-0">
                    Rp {{ number_format((float) $book->price, 0, ',', '.') }}
                </span>
            </span>
        </label>

        {{-- 2. Opsi Paket Langganan (Tetap di-looping jika bukunya ada di dalam paket) --}}
        @foreach ($book->subscriptionPackageBooks as $subscriptionPackageBook)
            @php($subscriptionPackage = $subscriptionPackageBook->subscriptionPackage)
            <label class="form-selectgroup-item">
                <input type="radio" name="purchase_option"
                    value="{{ $subscriptionPackageBook->subscription_package_id }}"
                    class="form-selectgroup-input">
                <span class="form-selectgroup-label purchase-option-card d-flex flex-column align-items-start justify-content-start p-3 border rounded">
                    <span class="text-muted small">{{ $subscriptionPackage->nama_package }}</span>
                    <span class="h3 fw-bold text-dark mb-0">
                        Rp {{ number_format((float) $subscriptionPackage->price, 0, ',', '.') }}
                    </span>
                </span>
            </label>
        @endforeach
    </div>
</div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-2">Konten ini dapat dibaca melalui aplikasi Gramedia Digital</h6>
                    <p class="text-muted small mb-2">
                        Download aplikasi Gramedia Digital yang tersedia di seluruh perangkat iOS dan Android
                    </p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-dark btn-sm">
                            <i class="ti ti-brand-google-play"></i> Google Play
                        </a>
                        <a href="#" class="btn btn-dark btn-sm">
                            <i class="ti ti-brand-apple"></i> App Store
                        </a>
                    </div>
                </div>
            </div>

            <hr>

            <div class="mb-4">
                <h6 class="fw-bold text-dark">Deskripsi</h6>
                <div id="bookdescription" class="text-muted description-clamped">
                    {!! $book->description !!}
                </div>
                <a href="javascript:void(0)" class="text-primary small fw-bold d-inline-flex align-items-center gap-1 mt-2">
                    <span id="readMoreText">Baca Selengkapnya</span>
                    <i id="readMoreIcon" class="ti ti-chevron-down"></i>
                </a>
            </div>

            <hr>

            <div class="mb-5">
                <h3 class="fw-bold text-dark mb-3">Informasi Buku</h3>
                <div class="row g-3">
                    <div class="col-6">
                        <div><span class="text-muted style-width-label">Bahasa</span>: <strong>{{ $book->language }}</strong></div>
                    </div>
                    <div class="col-6">
                        <div><span class="text-muted style-width-label">Tanggal Rilis</span>: <strong>{{ \Illuminate\Support\Carbon::parse($book->release_date)->format('d F Y') }}</strong></div>
                    </div>
                    <div class="col-6">
                        <div><span class="text-muted style-width-label">Penulis</span>: <strong>{{ $book->writer }}</strong></div>
                    </div>
                    <div class="col-6">
                        <div><span class="text-muted style-width-label">Halaman</span>: <strong>{{ $book->page_of_book }} Halaman</strong></div>
                    </div>
                    <div class="col-6">
                        <div><span class="text-muted style-width-label">Penerbit</span>: <strong>{{ $book->publisher }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="fixed-bottom border-top bg-white shadow-sm fixed-bottom-detail">
    <div class="container-xl px-5 py-2">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset($book->cover) }}" alt="{{ $book->title }}" class="rounded border" style="width: 48px; height: 50px; object-fit: cover;">
                <div>
                    <h4 class="mb-0 text-truncate fw-bold" style="max-width: 400px;">{{ $book->title }}</h4>
                    <span class="fw-bold text-dark">Rp {{ number_format((float) $book->price, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="d-flex gap-2">

                {{--hidden : data dari developer bukan pengguna ketika datanya diperlukan controller tapi bukan diisi atau dipilih pengguna--}}
                <input type="hidden" name="book_id" value="{{ $book->id }}">

                    <button type="submit" name="action" value="add_to_cart" class="btn btn-outline-primary px-4 py-2 fw-medium">Tambah ke Keranjang</button>
                    <button type="submit" name="action" value="buy_now" class="btn btn-primary px-4 py-2 fw-medium">Beli Sekarang</button>
               
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
    const toggleDescription = document.querySelector('a[href="javascript:void(0)"]');
    const description = document.getElementById('bookdescription');
    const readMoreIcon = document.getElementById('readMoreIcon');

    toggleDescription?.addEventListener('click', function () {
        const isClamped = description.classList.toggle('description-clamped') === false;
        document.getElementById('readMoreText').textContent = isClamped ? 'Tutup' : 'Baca Selengkapnya';
        readMoreIcon.classList.toggle('ti-chevron-down');
        readMoreIcon.classList.toggle('ti-chevron-up');
    });
</script>
@endpush
