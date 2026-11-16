@extends('layout.app')

@push('styles')
<style>
    .checkout-card { border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; }
    .checkout-qr { min-height: 290px; border-radius: 7px; border: 1px solid #e5e7eb; }
    .checkout-qr svg { width: 250px; height: 250px; max-width: 100%; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <h3 class="fs-4 fw-bold mb-3">Pembayaran</h3>
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="checkout-card checkout-qr p-4 text-center">
                <h4 class="fs-6 fw-bold mb-1">Scan QR Code</h4>
                <div class="text-secondary small mb-3">Gunakan aplikasi e-wallet atau m-banking Anda untuk memindai kode QR.</div>
                <div class="d-flex justify-content-center">{!! $qrCode !!}</div>
                <div class="text-secondary small mt-3">QR Code ini bersifat unik untuk paket dan pengguna ini.</div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="checkout-card p-4">
                <h5 class="fs-6 fw-bold mb-3">Detail Pesanan</h5>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-secondary">Nama Paket</span>
                    <strong>{{ $subscriptionPackage->nama_package }}</strong>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-secondary">Total Pembelian</span>
                    <strong>1</strong>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-secondary">Subtotal</span>
                    <strong>Rp {{ number_format((float) $subscriptionPackage->price, 0, ',', '.') }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong>Rp {{ number_format((float) $subscriptionPackage->price, 0, ',', '.') }}</strong>
                </div>
                <form method="POST" action="{{ route('subscription-packages.checkout', $subscriptionPackage) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Bayar Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
