@extends('layouts.app')

@section('title', 'Laporan Keuangan Excel - RT.011')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-custom border-0 shadow-sm text-center p-4">
            <div class="card-body">
                <div class="d-inline-flex bg-success bg-opacity-10 p-4 rounded-circle mb-4 text-success">
                    <i class="bi bi-file-earmark-spreadsheet-fill" style="font-size: 3rem;"></i>
                </div>
                <h3 class="fw-bold mb-2 text-dark">Unduh Laporan Excel RT.011</h3>
                <p class="text-muted mb-4">Laporan ini dibuat secara otomatis dengan 4 sheet data yang terperinci dan terintegrasi langsung dengan database</p>

                <!-- Detailed Sheets Info -->
                <div class="row g-3 text-start mb-4 justify-content-center">
                    <div class="col-md-10">
                        <div class="border rounded-3 p-3 bg-light">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-layers-half me-2 text-primary"></i>Struktur File Excel (.xlsx):</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <div>
                                        <strong>Sheet 1: Ringkasan</strong>
                                        <span class="text-muted d-block small">Berisi total saldo awal, pemasukan kas & keamanan, rincian pengeluaran, dan saldo akhir RT saat ini.</span>
                                    </div>
                                </li>
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <div>
                                        <strong>Sheet 2: Rekap_Bulanan_Warga</strong>
                                        <span class="text-muted d-block small">Rincian setoran iuran iuran warga dipisahkan per bulan untuk mempermudah pengecekan bulanan.</span>
                                    </div>
                                </li>
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <div>
                                        <strong>Sheet 3: Status_Warga</strong>
                                        <span class="text-muted d-block small">Daftar semua warga dengan akumulasi pembayaran, status lunas, dan status tunggakan iurannya.</span>
                                    </div>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <div>
                                        <strong>Sheet 4: Daftar_Transaksi</strong>
                                        <span class="text-muted d-block small">Semua catatan log transaksi secara detail dari yang terbaru hingga terlama (Buku kas umum).</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 10px;">
                        Kembali
                    </a>
                    <a href="{{ route('reports.export') }}" class="btn btn-success px-4 py-2" style="border-radius: 10px;">
                        <i class="bi bi-cloud-download me-2"></i>Unduh Laporan Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
