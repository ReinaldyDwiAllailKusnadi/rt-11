@extends('layouts.app')

@section('title', 'RT.011 - Portal Warga')

@section('styles')
<style>
    .text-light-green {
        color: #a7f3d0 !important; /* Soft green */
    }
    .text-success-light {
        color: #d1fae5 !important; /* Soft green success */
    }
    .text-danger-light {
        color: #fca5a5 !important; /* Soft red danger / pink */
    }
    .text-warning-light {
        color: #fde68a !important; /* Soft amber/yellow warning */
    }

    .resident-payment-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    }

    .payment-mobile-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        font-size: 0.95rem;
    }

    .payment-mobile-row span {
        color: #64748b;
    }

    .payment-mobile-row strong {
        color: #0f172a;
        white-space: nowrap;
    }

    .payment-months {
        margin-top: 4px;
        font-size: 0.82rem;
        color: #64748b;
        line-height: 1.35;
        word-break: break-word;
    }

    @media (max-width: 575.98px) {
        #paymentInfoSection .card-header {
            padding-left: 16px !important;
            padding-right: 16px !important;
        }

        #paymentInfoSection .card-body {
            padding-left: 16px !important;
            padding-right: 16px !important;
        }

        #paymentInfoSection h4 {
            font-size: 1.15rem;
            line-height: 1.3;
        }

        .resident-payment-card {
            padding: 14px;
            border-radius: 14px;
        }

        .badge-tunggak,
        .badge-lunas {
            font-size: 0.68rem;
            padding: 6px 9px;
        }
    }
</style>
@endsection



@section('content')
<div class="row g-4">
    <!-- Header Hero Card -->
    <div class="col-12">
        <div class="card card-custom overflow-hidden border-0" style="background: linear-gradient(135deg, var(--primary-color) 0%, #2c5375 100%);">
            <div class="card-body p-4 p-md-5 text-white">
                <div class="row align-items-center">
                    <div class="col-md-8 text-center text-md-start">
                        <span class="badge bg-light text-primary px-3 py-2 rounded-pill mb-3 fw-bold">Selamat Datang</span>
                        <h1 class="display-5 fw-bold mb-2 hero-title">Portal Informasi Warga RT.011</h1>
                        <p class="lead mb-4 text-white-50">Transparansi Keuangan dan Administrasi Rukun Tetangga 011 / RW 003 Perumahan Karanggintung.</p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-md-start">
                            <a href="#paymentInfoSection" class="btn btn-secondary-custom px-4 py-2">
                                <i class="bi bi-search me-2"></i>Cek Status Pembayaran
                            </a>
                            @auth
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light px-4 py-2">
                                    <i class="bi bi-speedometer2 me-2"></i>Kembali ke Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-light px-4 py-2">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login Admin
                                </a>
                            @endauth
                        </div>
                    </div>
                    <div class="col-md-4 d-none d-md-block text-center">
                        <i class="bi bi-wallet2 text-white-50" style="font-size: 8rem; color: var(--secondary-color) !important;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Cards / Ringkasan Keuangan -->
    <div class="col-12">
        @include('components.financial-summary', ['summary' => $summary])
    </div>

    <!-- Payment Status Checking / Public Table -->
    <div class="col-12" id="paymentInfoSection">
        <div class="card card-custom border-0 my-4">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--primary-color);"><i class="bi bi-credit-card-2-front me-2" style="color: var(--secondary-color);"></i>Status Iuran Warga (Tahun 2026)</h4>
                    <p class="text-muted mb-0 small">Bulan Berjalan dihitung sejak Januari 2026</p>
                </div>
                <!-- Search bar -->
                <form action="{{ route('public.dashboard') }}#paymentInfoSection" method="GET" class="d-flex flex-column flex-sm-row gap-2 w-100 w-sm-auto mt-2 mt-sm-0">
                    <input type="hidden" name="show_info" value="1">
                    <input type="text" name="search" class="form-control form-control-sm flex-grow-1" placeholder="Cari nama / no rumah..." value="{{ $search }}" style="min-width: 200px;">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-secondary-custom btn-sm w-100 w-sm-auto"><i class="bi bi-search me-1"></i> Cari</button>
                        @if($search)
                            <a href="{{ route('public.dashboard') }}#paymentInfoSection" class="btn btn-outline-secondary btn-sm w-100 w-sm-auto"><i class="bi bi-x-circle me-1"></i> Reset</a>
                        @endif
                    </div>
                </form>
            </div>
            
            <div class="card-body px-4 pb-4">
                <!-- 1. Tampilan Desktop & Tablet Besar -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" style="min-width: 900px;">
                            <thead class="table-light text-muted uppercase small">
                                <tr>
                                    <th>No Rumah</th>
                                    <th>Nama Warga</th>
                                    <th>Kas RT (20rb)</th>
                                    <th>Bulan Kas Dibayar</th>
                                    <th>Keamanan (55rb)</th>
                                    <th>Bulan Keamanan Dibayar</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginatedResidents as $warga)
                                    <tr>
                                        <td class="fw-semibold">{{ $warga->no_rumah }}</td>
                                        <td>{{ $warga->name }}</td>
                                        <td>Rp {{ number_format($warga->total_kas, 0, ',', '.') }}</td>
                                        <td>
                                            <small class="text-muted d-block text-wrap" style="max-width: 180px; font-size: 0.8rem;">
                                                {{ $warga->bulan_kas_list ?: '-' }}
                                            </small>
                                        </td>
                                        <td>Rp {{ number_format($warga->total_keamanan, 0, ',', '.') }}</td>
                                        <td>
                                            <small class="text-muted d-block text-wrap" style="max-width: 180px; font-size: 0.8rem;">
                                                {{ $warga->bulan_keamanan_list ?: '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($warga->status_pembayaran === 'LUNAS')
                                                <span class="badge badge-lunas px-3 py-2 rounded-pill small">LUNAS</span>
                                            @else
                                                <span class="badge badge-tunggak px-3 py-2 rounded-pill small">
                                                    TUNGGAKAN {{ $warga->tunggakan }} BLN
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('receipt.show', $warga->id) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-3">
                                                <i class="bi bi-printer me-1"></i> Kwitansi
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Data warga tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Tampilan Mobile (Card per Warga) -->
                <div class="d-md-none">
                    @forelse($paginatedResidents as $warga)
                        <div class="resident-payment-card">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <span class="badge bg-light text-dark border mb-2">{{ $warga->no_rumah }}</span>
                                    <h6 class="fw-bold mb-0">{{ $warga->name }}</h6>
                                </div>
                                @if($warga->status_pembayaran === 'LUNAS')
                                    <span class="badge badge-lunas rounded-pill">LUNAS</span>
                                @else
                                    <span class="badge badge-tunggak rounded-pill text-nowrap">
                                        TUNGGAKAN {{ $warga->tunggakan }} BLN
                                    </span>
                                @endif
                            </div>

                            <div class="payment-mobile-row">
                                <span>Kas RT</span>
                                <strong>Rp {{ number_format($warga->total_kas, 0, ',', '.') }}</strong>
                            </div>
                            <div class="payment-months">
                                {{ $warga->bulan_kas_list ?: '-' }}
                            </div>

                            <div class="payment-mobile-row mt-3">
                                <span>Keamanan</span>
                                <strong>Rp {{ number_format($warga->total_keamanan, 0, ',', '.') }}</strong>
                            </div>
                            <div class="payment-months">
                                {{ $warga->bulan_keamanan_list ?: '-' }}
                            </div>

                            <a href="{{ route('receipt.show', $warga->id) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 mt-3">
                                <i class="bi bi-printer me-1"></i> Cetak Kwitansi
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">Data warga tidak ditemukan.</div>
                    @endforelse
                </div>
                
                <!-- Pagination links -->
                <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="small text-muted">Menampilkan {{ $paginatedResidents->firstItem() ?? 0 }} sampai {{ $paginatedResidents->lastItem() ?? 0 }} dari {{ $paginatedResidents->total() }} warga</span>
                    <div class="overflow-auto">
                        {{ $paginatedResidents->fragment('paymentInfoSection')->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
