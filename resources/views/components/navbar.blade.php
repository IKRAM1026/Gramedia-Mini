<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- LOGO -->
        <a href="{{ route('home') }}" aria-label="Tabler" class="navbar-brand navbar-brand-autodark me-3">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQF2pl7ttfSbXAjrE73NFvpYodC8LDrZHK4ZHZinfxNMOlAfOZT8OMatMUh&s=10"
                alt="Ebook"
                class="navbar-brand-image">

            <h1 class="mb-0">Ebook</h1>
        </a>

        <!-- NAVBAR -->
        <ul class="navbar-nav mx-auto w-50 py-2">

            @if (Auth::check() && Auth::user()->role == 'admin')

                {{-- NAVBAR ADMIN --}}
                <li class="nav-item {{ request()->routeis('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        Dashboard Admin
                    </a>
                </li>

                <li class="nav-item {{ request()->routeis('admin.book-categories.') ? 'active' : '' }}">
                    <a href="{{ route('admin.book-categories.index') }}" class="nav-link">
                        Kategori Buku
                    </a>
                </li>

                <li class="nav-item" {{ request()->routeis('admin.subscription-packages.') ? 'active' : '' }}>
                    <a href="{{ route('admin.subscription-packages.index') }}" class="nav-link">
                        Paket Langganan
                    </a>
                </li>

                <li class="nav-item {{ request()->routeis('admin.books.') ? 'active' : '' }}">
                    <a href="{{ route('admin.books.index') }}" class="nav-link">
                        Buku
                    </a>
                </li>


            @else

                {{-- KATEGORI --}}
                <div class="dropdown">
                    <a href="#" class="btn btn-light dropdown-toggle me-2"
                        data-bs-toggle="dropdown">
                        Kategori
                    </a>

                    <div class="dropdown-menu dropdown-menu-card"
                        style="min-width: 600px;">

                        <div class="p-3">
                            <div class="row g-2">
                                @forelse ($bookCategories as $category)
                                    <div class="col-3">
                                        <a href="{{ route('books.category', $category) }}" class="card py-2 text-decoration-none">
                                            <div class="card-body text-dark text-center">
                                                {{ $category->name }}
                                            </div>
                                        </a>
                                    </div>
                                @empty
                                    <div class="col-12 text-secondary">
                                        Belum ada kategori buku.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEARCH --}}
                <div class="input-icon w-100">
                    <input type="text"
                        class="form-control form-control-rounded"
                        placeholder="Cari Judul, Produk, Penulis...">

                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon">

                            <path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />

                        </svg>
                    </span>
                </div>

                {{-- KERANJANG --}}
                <div class="ms-2 mt-3">
                    <i class="fa fa-shopping-cart"></i>
                </div>

            @endif

        </ul>

        {{-- BUTTON LOGIN / LOGOUT --}}
        <div class="flex-row order-md-last ms-auto">

            @if (Auth::check())
                @if (Auth::user()->role == 'admin')
                    <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
                @else
                    <a href="{{ route('account') }}" class="btn btn-light ">
                        <span class="avatar avatar-md text-primary me-1">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        
                    </a>
                    <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
                @endif

            @else

                <a href="{{ route('login') }}" class="btn btn-primary">
                    Masuk
                </a>

                <a href="{{ route('register') }}" class="btn btn-light">
                    Daftar
                </a>

            @endif

        </div>

    </div>
</header>