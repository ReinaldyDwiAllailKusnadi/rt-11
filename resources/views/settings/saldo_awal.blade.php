@extends('layouts.app')

@section('title', 'Atur Saldo Awal - RT.011')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h4 class="fw-bold mb-1" style="color: var(--primary-color);"><i class="bi bi-sliders me-2" style="color: var(--secondary-color);"></i>Konfigurasi Saldo Awal</h4>
                <p class="text-muted small mb-0">Atur nominal saldo awal Kas RT dan Keamanan untuk pembukuan tahun 2026</p>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('settings.saldo-awal.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="saldo_awal_kas" class="form-label small fw-bold">Saldo Awal Kas RT (Rp)</label>
                        <input type="number" name="saldo_awal_kas" id="saldo_awal_kas" class="form-control" value="{{ old('saldo_awal_kas', $saldoAwalKas) }}" min="0" required>
                        <div class="form-text small text-muted">Saldo awal Kas RT sebelum transaksi iuran bulan berjalan masuk.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="saldo_awal_keamanan" class="form-label small fw-bold">Saldo Awal Keamanan (Rp)</label>
                        <input type="number" name="saldo_awal_keamanan" id="saldo_awal_keamanan" class="form-control" value="{{ old('saldo_awal_keamanan', $saldoAwalKeamanan) }}" min="0" required>
                        <div class="form-text small text-muted">Saldo awal dana Keamanan sebelum iuran satpam bulan berjalan masuk.</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">Batal</a>
                        <button type="submit" class="btn btn-secondary-custom btn-sm">
                            <i class="bi bi-save me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
