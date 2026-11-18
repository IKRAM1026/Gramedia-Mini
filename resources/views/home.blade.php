@extends('layout.app')

@push('styles')
    <style>
        .slick-prev:before, .slick-next::before {
            color: #333;
        }

        .book-scroll {
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            padding-bottom: 10px;
            scrollbar-width: thin;
            scrollbar-color: rgba(15, 23, 42, 0.35) transparent;
            overscroll-behavior-inline: contain;
        }

        .book-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .book-scroll::-webkit-scrollbar-thumb {
            background: rgba(15, 23, 42, 0.3);
            border-radius: 0;
        }

        .book-track {
            display: flex;
            gap: 16px;
            min-width: max-content;
            padding-bottom: 4px;
        }

        .book-card {
            width: 220px;
            flex: 0 0 220px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        .book-card img {
            width: 100%;
            height: 260px;
            object-fit: cover;
            display: block;
        }

        .book-card .card-body {
            padding: 12px 12px 14px;
        }

        .book-tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 8px;
            border-radius: 999px;
            background: #f3f4f6;
            font-size: 0.7rem;
            font-weight: 600;
            color: #374151;
        }

    </style>
@endpush

@section('content')
    

<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{session('success')}}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{session('error')}}
        </div>
    @endif

    <div id="carousel-sample" class="carousel slide carousel-sm" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carousel-sample" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carousel-sample" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carousel-sample" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" alt="" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS4ZCH2QoLoe6n5NSWmV07N6tsxJ8WtE-UIrS_0ietIwQ&s=10" />
            </div>      
            <div class="carousel-item">
                <img class="d-block w-100" alt="" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS4ZCH2QoLoe6n5NSWmV07N6tsxJ8WtE-UIrS_0ietIwQ&s=10" />
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" alt="" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS4ZCH2QoLoe6n5NSWmV07N6tsxJ8WtE-UIrS_0ietIwQ&s=10" />
            </div>
        </div>

        <style>
            .carousel-sm {
                max-width: 1180px;
                margin: 0 auto;
            }

            .carousel-sm .carousel-inner {
                border-radius: 12px;
                overflow: hidden;
                background: #f5f5f5;
            }

            .carousel-sm .carousel-item {
                text-align: center;
            }

            .carousel-sm .carousel-item img {
                display: block;
                width: 100%;
                height: auto;
                max-height: 420px;
                object-fit: contain;
                object-position: center;
                margin: 0 auto;
                background: #f5f5f5;
            }
        </style>

        <button class="carousel-control-prev" type="button" data-bs-target="#carousel-sample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carousel-sample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

<div class="mt-4">
    <div class="d-flex align-items-center gap-3 mb-3">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning text-dark" style="width: 44px; height: 44px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                <path d="M3 18h18l-1.5-9L15 13l-3-7-3 7-4.5-4L3 18Zm2 0v2h14v-2H5Z"/>
            </svg>
        </span>
        <h2 class="mb-0 text-dark fw-bold">Paket Langganan</h2>
    </div>
    <div class="row g-4">
        @forelse ($subscriptionPackages as $subscriptionPackage)
            <div class="col-md-4">
                <a href="{{ route('subscription-packages.show', $subscriptionPackage) }}" class="card h-100 text-decoration-none" style="background: linear-gradient(135deg, #ffffff, {{ $subscriptionPackage->color ?: '#e5e7eb' }} 100%);">
                    <div class="card-body text-center text-dark d-flex flex-column justify-content-center">
                        <h2 style="font-weight: bold;">{{ $subscriptionPackage->nama_package }}</h2>
                        <p class="text-secondary" style="font-weight: bold;">{{ $subscriptionPackage->description }}</p>
                        <div>
                            Rp <span style="font-size: 2rem; font-weight: bold; color: #f59e0b;">{{ number_format((float) $subscriptionPackage->price, 2, ',', '.') }}</span>
                            <br>
                            <span class="text-secondary" style="font-weight: bold;">/30 days</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <p class="text-secondary mb-0">Belum ada paket langganan yang tersedia.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="mt-4">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <h2 class="mt-3 text-dark">Buku Baru Dirilis</h2>
                </div>

                <div class="book-scroll">
                    <div class="book-track">
                        @forelse ($latestBooks as $book)
                            <a href="{{ route('books.show', $book) }}" class="text-decoration-none">
                                <article class="book-card">
                                    <img src="{{ asset($book->cover) }}" alt="{{ $book->title }}">
                                    <div class="card-body">
                                        <span class="book-tag mb-2"><i class="fa-solid fa-mobile me-1"></i> PDF</span>
                                        <div class="text-secondary small mb-1">{{ $book->writer }}</div>
                                        <h5 class="text-dark mb-2" style="white-space: normal;">{{ $book->title }}</h5>
                                        <div class="fw-bold text-dark">Rp {{ number_format($book->price, 0, ',', '.') }}</div>
                                    </div>
                                </article>
                            </a>
                        @empty
                            <p class="text-center">Tidak ada buku terbaru.</p>
                        @endforelse
                    </div>
                </div>
            </div>

{{-- buku gratis --}}
<div class="mt-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <h2 class="mt-3 text-dark fw-bold">Buku Gratis</h2>
    </div>
    <div class="book-scroll">
        <div class="book-track">
            @php
                $freeDummyBooks = [
                    ['cover' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQX59cz9y5LsU7ufHsf4tgr68rO9ZQZLWMJPt9tgZWG5Q&s=10', 'author' => 'penulis', 'title' => 'Bintang'],
                    ['cover' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvrBCWT5hZc1YFiK-r4a38rJKFbK_-fP3pH1mNayZVBw&s=10', 'author' => 'penulis', 'title' => 'Judul Buku'],
                    ['cover' => 'https://www.gramedia.com/blog/content/images/2019/10/9786023946532_amor-fati_cintai-takdirmu_batch.jpg', 'author' => 'penulis', 'title' => 'Judul Buku'],
                    ['cover' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQMWIKZHuJC1aTHG_MMMH5_epWRf2P_G7G0I_eL3Lw4_g&s=10', 'author' => 'penulis', 'title' => 'Judul Buku'],
                    ['cover' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQYxiawOf0LxeoHsfnS6EsUrYGQAUkY0xWczSSpwc_tLg&s=10', 'author' => 'penulis', 'title' => 'Judul Buku'],
                    ['cover' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRpNq-MAw2jQN7pLLOI_8FsiNb1-G1WtrSRpHDgwLb5pA&s=10', 'author' => 'penulis', 'title' => 'Judul Buku'],
                ];
            @endphp

            @foreach ($freeDummyBooks as $book)
                <article class="book-card">
                    <img src="{{ $book['cover'] }}" alt="{{ $book['title'] }}">
                    <div class="card-body">
                        <span class="book-tag mb-2"><i class="fa-solid fa-mobile me-1"></i> PDF</span>
                        <div class="text-secondary small mb-1">{{ $book['author'] }}</div>
                        <h5 class="text-dark mb-2" style="white-space: normal;">{{ $book['title'] }}</h5>
                        <div class="fw-bold text-dark">Rp 0</div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</div>

    </main>

@endsection
