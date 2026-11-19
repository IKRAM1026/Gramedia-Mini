@extends('layout.app')

@section('content')

<div class="container my-4">
    <h2 class="fw-semibold fs-3 mb-4">Keranjang Belanja</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">

        <!-- ============ BAGIAN KIRI ============ -->
        <div class="col-lg-8">

            <!-- Header jumlah buku terpilih -->
            <div class="card border rounded-3 p-2 mb-3 shadow-sm">
                <div class="form-check d-flex align-items-center">
                    <label class="form-check-label fw-medium">
                        <span id="cart-count">{{ $carts->count() }}</span> Buku Terpilih
                    </label>
                </div>
            </div>

            <!-- Daftar Item Keranjang -->
            @foreach ($carts as $cart)
            <div class="card border rounded-3 p-3 mb-3 shadow-sm position-relative">
                <div class="d-flex align-items-center">
                    <div class="form-check me-3">
                        <input class="form-check-input item-checkbox" type="checkbox"
                            name="selected_carts[]" value="{{ $cart->id }}"
                            data-price="{{ $cart->display_price }}" checked>
                    </div>

                    <div class="flex-shrink-0 me-3">
                        <img src="{{ $cart->book->cover ?? '' }}" width="95" height="135"
                            alt="{{ $cart->book->title }}" class="rounded"
                            style="width: 95px; height: 135px; object-fit: cover;">
                    </div>

                    <div class="flex-grow-1">
                        <div class="text-muted small">{{ $cart->book->writer ?? '' }}</div>
                        <h5 class="fw-semibold mb-0 text-dark">{{ $cart->book->title }}</h5>
                    </div>

                    <div class="text-end align-self-start">
                        <h5 class="fw-semibold mb-1">Rp{{ number_format($cart->display_price, 0, ',', '.') }}</h5>

                        @if ($cart->subscription_package_id == null)
                            <span class="badge bg-secondary text-white px-3 py-2 mt-1 fw-semibold rounded-2">Satuan</span>
                        @else
                            <span class="badge bg-success text-white px-3 py-2 mt-1 fw-semibold rounded-2">{{ $cart->subscriptionPackage->nama_package }}</span>
                        @endif

                        <form action="{{ route('carts.destroy', $cart->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link"
                                style="padding: 0 !important; position: absolute; bottom: 10px; right: 15px;">
                                <i class="fa fa-solid fa-trash text-danger fs-3 mt-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <!-- ============ BAGIAN KANAN ============ -->
        <div class="col-lg-4">
            <div class="card border rounded-4 p-4 shadow-sm position-sticky" style="top: 20px">
                <h6 class="fw-semibold mb-4">Ringkasan Keranjang</h6>

                <div class="d-flex justify-content-between mb-4 text-muted">
                    <span>Total Belanja (<span id="total-selected-count">{{ $carts->count() }}</span> Buku)</span>
                </div>

                <div class="mb-2">
                    @foreach ($carts as $cart)
                        <div class="summary-row d-flex justify-content-between small mb-1 {{ $cart->needs_package ? 'text-danger' : 'text-muted' }}"
                            data-cart-id="{{ $cart->id }}">
                            <span class="text-truncate pe-3">{{ $cart->book->title }}</span>
                            <span class="fw-medium" style="text-align: right;">
                                Rp{{ number_format($cart->display_price, 0, ',', '.') }}
                                @if ($cart->needs_package)
                                    <br><small class="text-danger">Perlu Membeli Paket {{ $cart->subscriptionPackage->nama_package }}</small>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-between align-items-center mb-3 text-muted">
                    <span>Total Belanja</span>
                    <h4 id="total-price-text" class="text-dark mb-0">Rp{{ number_format($totalPrice, 0, ',', '.') }}</h4>
                </div>

                <form method="POST" action="#">
                    @csrf
                    <div id="checkout-inputs"></div>
                    <button type="submit" id="btn-checkout" class="btn btn-primary w-100 py-3 rounded-pill fw-semibold">
                        Checkout
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const totalPriceText = document.querySelector('#total-price-text');
        const totalCountText = document.querySelector('#total-selected-count');
        const cartCount = document.querySelector('#cart-count');
        const btnCheckout = document.querySelector('#btn-checkout');
        const checkoutInputs = document.querySelector('#checkout-inputs');

        const rupiah = n => 'Rp' + n.toLocaleString('id-ID');

        function updateSummary() {
            const checked = [...checkboxes].filter(cb => cb.checked);
            const total = checked.reduce((sum, cb) => sum + parseInt(cb.dataset.price || 0), 0);

            if (totalPriceText) totalPriceText.textContent = rupiah(total);
            if (totalCountText) totalCountText.textContent = checked.length;
            if (cartCount) cartCount.textContent = checked.length;

            checkboxes.forEach(cb => {
                const row = document.querySelector(`.summary-row[data-cart-id="${cb.value}"]`);
                if (row) {
                    if (cb.checked) {
                        row.classList.remove('d-none');
                    } else {
                        row.classList.add('d-none');
                    }
                }
            });

            if (btnCheckout) {
                btnCheckout.disabled = (checked.length === 0);
            }

            if (checkoutInputs) {
                checkoutInputs.innerHTML = '';
                checked.forEach(cb => {
                    checkoutInputs.innerHTML += `<input type="hidden" name="selected_carts[]" value="${cb.value}">`;
                });
            }
        }

        // Jalankan updateSummary setiap kali checkbox dicentang atau di-uncheck
        checkboxes.forEach(cb => cb.addEventListener('change', updateSummary));

        // Jalankan pertama kali saat halaman dimuat
        updateSummary();
    });
</script>
@endpush