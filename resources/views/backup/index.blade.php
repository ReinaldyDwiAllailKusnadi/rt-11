@extends('layouts.app')

@section('title', 'Backup & Restore - RT.011')

@section('content')
<div class="row g-4 justify-content-center">
    <!-- 1. EXPORT BACKUP -->
    <div class="col-md-6">
        <div class="card card-custom border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-cloud-arrow-down-fill me-2 text-success"></i>Backup Data (Export)</h4>
                <p class="text-muted small mb-0">Unduh seluruh isi database RT.011 ke dalam format file JSON</p>
            </div>
            
            <div class="card-body px-4 pb-4 pt-2 d-flex flex-column justify-content-between">
                <p class="text-muted small">File backup ini menyimpan semua data warga, transaksi kas, transaksi keamanan, pengeluaran, serta konfigurasi saldo awal. File ini dapat Anda simpan secara lokal di komputer sebagai arsip cadangan keamanan.</p>
                <div class="mt-4">
                    <a href="{{ route('backup.export') }}" class="btn btn-success w-100 py-2.5" style="border-radius: 10px;">
                        <i class="bi bi-download me-2"></i>Unduh File Backup JSON
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. IMPORT BACKUP -->
    <div class="col-md-6">
        <div class="card card-custom border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-cloud-arrow-up-fill me-2 text-danger"></i>Restore Data (Import)</h4>
                <p class="text-muted small mb-0">Unggah file backup JSON untuk memulihkan seluruh data RT.011</p>
            </div>
            
            <div class="card-body px-4 pb-4 pt-2">
                <div class="alert alert-warning border-0 p-3 mb-3" style="border-radius: 10px;">
                    <span class="small fw-semibold text-dark d-block mb-1">⚠️ Perhatian Khusus:</span>
                    <span class="small text-muted d-block" style="line-height: 1.3;">Proses restore akan menghapus seluruh data warga, transaksi, dan pengaturan yang ada di database saat ini, kemudian menimpanya dengan data dari file backup. Pastikan file backup valid.</span>
                </div>
                
                <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="backup_file" class="form-label small fw-bold">Pilih File Backup (.json)</label>
                        <input type="file" name="backup_file" id="backup_file" class="form-control" accept=".json" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 py-2.5" style="border-radius: 10px;" onsubmit="return confirm('Apakah Anda yakin ingin memulihkan database dari file ini? Semua data saat ini akan ditimpa!');">
                        <i class="bi bi-arrow-repeat me-2"></i>Mulai Proses Restore Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
