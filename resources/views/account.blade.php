@extends('layout.app')

@push('styles')
<style>
    .account-summary { border-right: 1px solid #e5e7eb; min-height: 180px; }
    .account-card { border: 1px solid #e5e7eb; border-radius: 6px; }
    .account-avatar { width: 44px; height: 44px; border-radius: 50%; background: #eaf1ff; color: #456; font-weight: 700; }
    .history-row { border-bottom: 1px solid #eef0f2; }
    .history-row:last-child { border-bottom: 0; }
    .transaction-filter { min-width: 180px; }
</style>
@endpush

@section('content')
<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="account-card bg-white mb-4">
        <div class="row g-0">
            <div class="col-md-4 account-summary p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="account-avatar d-flex align-items-center justify-content-center me-3">
                        {{ strtoupper( substr($user->name, 0, 1)) }}
                    </div>
                    <strong>{{ $user->name }}</strong>
                </div>
                <div class="text-secondary small fw-bold mb-3">Profil Summary</div>
                <div class="d-flex justify-content-between mb-2 small text-secondary">
                    <span>Koleksi Buku</span>
                    <strong class="text-dark">{{ $bookHistories->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between small text-primary">
                    <span>Langganan</span>
                    <strong>{{ $subscriptionHistories->count() }}</strong>
                </div>
            </div>
            <div class="col-md-8 p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h3 class="mb-0 fs-5 fw-bold">Riwayat Pembelian</h3>
                    <form method="GET" action="{{ route('account') }}">
                        <div class="text-secondary small mb-1 text-end">Jenis Transaksi</div>
                        <select name="type" class="form-select form-select-sm transaction-filter" onchange="this.form.submit()">
                            <option value="subscription" {{ $type === 'subscription' ? 'selected' : '' }}>Paket Langganan</option>
                            <option value="book" {{ $type === 'book' ? 'selected' : '' }}>Buku</option>
                        </select>
                    </form>
                </div>

                <div>
                    <div class="border rounded">
                        @if ($type === 'subscription')
                            @forelse ($subscriptionHistories as $history)
                            <div class="history-row p-3">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <div class="fw-bold">{{ $history->subscriptionPackage->nama_package }}</div>
                                        <div class="small text-secondary mt-1">
                                            Dibeli {{ $history->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-secondary">Berakhir</div>
                                        <div class="fw-bold">{{ $history->expired_date->format('d M Y') }}</div>
                                        <div class="text-primary fw-bold mt-1">
                                            Rp {{ number_format((float) $history->subscriptionPackage->price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-secondary small p-3">Belum ada riwayat paket langganan.</div>
                        @endforelse
                        @else
                            @forelse ($bookHistories as $history)
                                <div class="history-row p-3">
                                    <div class="d-flex justify-content-between gap-3">
                                        <div>
                                            @foreach ($history->checkoutbook as $checkoutBook)
                                                <div class="fw-bold">{{ $checkoutBook->book->title }}</div>
                                            @endforeach
                                            <div class="small text-secondary mt-1">
                                                Dibeli {{ $history->created_at->format('d M Y, H:i') }}
                                            </div>
                                        </div>
                                        <div class="text-primary fw-bold">
                                            Rp {{ number_format((float) $history->total_price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-secondary small p-3">Belum ada riwayat pembelian buku.</div>
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
