@extends('layouts.app')

@section('title', 'Admin Dashboard - RT.011')
@section('robots', 'noindex, nofollow')

@section('content')
<div class="row g-4">
    <!-- Header Admin Card -->
    <div class="col-12">
        <div class="card card-custom overflow-hidden border-0" style="background: linear-gradient(135deg, var(--primary-color) 0%, #1e4b6d 100%);">
            <div class="card-body p-4 p-md-5 text-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge bg-secondary-custom px-3 py-2 rounded-pill mb-3 fw-bold">Panel Administrator</span>
                        <h2 class="fw-bold mb-2">Selamat Datang, Admin RT.011</h2>
                        <p class="lead mb-0 text-white-50">Kelola keuangan iuran kas, iuran keamanan, data warga, surat pengantar, dan laporan RT secara terpusat.</p>
                    </div>
                    <div class="col-md-4 text-md-end text-center mt-3 mt-md-0">
                        <span class="text-white bg-white bg-opacity-10 px-3 py-2 rounded-pill small border border-white border-opacity-10">
                            <i class="bi bi-person-circle me-2 text-warning"></i>Admin Aktif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="col-12">
        @include('components.financial-summary', ['summary' => $summary])
    </div>

    <!-- Quick Navigation Menu -->
    <div class="col-md-8">
        <div class="card card-custom border-0 my-2 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h4 class="fw-bold mb-1" style="color: var(--primary-color);"><i class="bi bi-grid-fill me-2" style="color: var(--secondary-color);"></i>Navigasi Cepat Administrasi</h4>
                <p class="text-muted small mb-0">Akses fitur-fitur manajemen iuran RT.011</p>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('payments.create') }}" class="btn btn-outline-primary d-flex flex-column align-items-center justify-content-center p-4 h-100 border-2 rounded-4 text-decoration-none">
                            <i class="bi bi-plus-circle-fill mb-2 text-primary" style="font-size: 2rem;"></i>
                            <span class="fw-bold text-dark text-center">Input Transaksi</span>
                            <span class="text-muted small text-center mt-1">Kas, Keamanan, & Pengeluaran</span>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('residents.index') }}" class="btn btn-outline-primary d-flex flex-column align-items-center justify-content-center p-4 h-100 border-2 rounded-4 text-decoration-none">
                            <i class="bi bi-people-fill mb-2 text-success" style="font-size: 2rem;"></i>
                            <span class="fw-bold text-dark text-center">Data Warga</span>
                            <span class="text-muted small text-center mt-1">Kelola 66 Warga & Alamat</span>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('payments.status') }}" class="btn btn-outline-primary d-flex flex-column align-items-center justify-content-center p-4 h-100 border-2 rounded-4 text-decoration-none">
                            <i class="bi bi-file-earmark-check-fill mb-2 text-info" style="font-size: 2rem;"></i>
                            <span class="fw-bold text-dark text-center">Status Bayar</span>
                            <span class="text-muted small text-center mt-1">Cek Lunas & Tunggakan</span>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('letters.index') }}" class="btn btn-outline-primary d-flex flex-column align-items-center justify-content-center p-4 h-100 border-2 rounded-4 text-decoration-none">
                            <i class="bi bi-file-text-fill mb-2 text-warning" style="font-size: 2rem;"></i>
                            <span class="fw-bold text-dark text-center">Surat RT</span>
                            <span class="text-muted small text-center mt-1">Surat Domisili & Usaha</span>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-primary d-flex flex-column align-items-center justify-content-center p-4 h-100 border-2 rounded-4 text-decoration-none">
                            <i class="bi bi-file-earmark-spreadsheet-fill mb-2 text-danger" style="font-size: 2rem;"></i>
                            <span class="fw-bold text-dark text-center">Laporan Excel</span>
                            <span class="text-muted small text-center mt-1">Unduh Rekap Laporan</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Password Change / Quick Config panel -->
    <div class="col-md-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="card card-custom border-0 my-2">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-gear-fill me-2" style="color: var(--secondary-color);"></i>Konfigurasi Awal</h5>
                    </div>
                    <div class="card-body px-4 pb-4 pt-1">
                        <p class="text-muted small">Atur saldo awal kas dan keamanan untuk memulai pembukuan.</p>
                        <a href="{{ route('settings.saldo-awal') }}" class="btn btn-primary-custom btn-sm w-100">
                            <i class="bi bi-sliders me-1"></i>Atur Saldo Awal
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card card-custom border-0 my-2">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-key-fill me-2" style="color: var(--secondary-color);"></i>Ganti Password</h5>
                    </div>
                    <div class="card-body px-4 pb-4 pt-1">
                        <form action="{{ route('admin.change-password') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="old_password" class="form-label small fw-bold">Password Lama</label>
                                <input type="password" name="old_password" id="old_password" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label small fw-bold">Password Baru</label>
                                <input type="password" name="new_password" id="new_password" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label small fw-bold">Konfirmasi Password Baru</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control form-control-sm" required>
                            </div>
                            <button type="submit" class="btn btn-secondary-custom btn-sm w-100">
                                <i class="bi bi-save me-1"></i>Simpan Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
