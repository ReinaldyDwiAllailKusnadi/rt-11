@php
    $formatRupiah = function ($value) {
        $value = (int) $value;
        if ($value < 0) {
            return '-Rp ' . number_format(abs($value), 0, ',', '.');
        }
        return 'Rp ' . number_format($value, 0, ',', '.');
    };

    $formatPengeluaran = function ($value) {
        $value = (int) $value;
        if ($value > 0) {
            return '-Rp ' . number_format($value, 0, ',', '.');
        }
        return 'Rp 0';
    };
@endphp

<div class="card card-custom border-0 text-white animate__animated animate__fadeIn" style="background: linear-gradient(135deg, var(--secondary-color) 0%, #178b86 100%);">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
            <h4 class="fw-bold mb-0 d-flex align-items-center financial-summary-title">
                <i class="bi bi-bar-chart-line-fill me-2" style="color: #fff;"></i> 📊 Ringkasan Keuangan RT.011
            </h4>
            <span class="badge px-3 py-2 rounded-pill small" style="background: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; white-space: normal; text-align: left;">
                <i class="bi bi-clock me-1"></i> Update: {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}
            </span>
        </div>
        
        <div class="row g-4">
            <!-- Column 1: Saldo Awal & Pemasukan -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="h-100 p-3 rounded-4" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <h6 class="fw-bold text-white text-uppercase mb-3 pb-2 border-bottom border-white border-opacity-25" style="letter-spacing: 0.5px;">
                        <i class="bi bi-wallet2 me-1"></i> Saldo Awal
                    </h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 small">Saldo Awal Kas RT</span>
                        <span class="fw-bold text-white financial-summary-amount text-break">{{ $formatRupiah($summary['saldo_awal_kas'] ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="text-white-50 small">Saldo Awal Keamanan</span>
                        <span class="fw-bold text-white financial-summary-amount text-break">{{ $formatRupiah($summary['saldo_awal_keamanan'] ?? 0) }}</span>
                    </div>
                    
                    <h6 class="fw-bold text-white text-uppercase mb-3 pb-2 border-bottom border-white border-opacity-25" style="letter-spacing: 0.5px;">
                        <i class="bi bi-arrow-up-circle me-1"></i> Pemasukan (Iuran)
                    </h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 small">Kas RT</span>
                        <span class="fw-bold text-light-green financial-summary-amount text-break">{{ $formatRupiah($summary['kas_rt'] ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-white-50 small">Keamanan</span>
                        <span class="fw-bold text-light-green financial-summary-amount text-break">{{ $formatRupiah($summary['keamanan'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Column 2: Pengeluaran -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="h-100 p-3 rounded-4" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <h6 class="fw-bold text-white text-uppercase mb-3 pb-2 border-bottom border-white border-opacity-25" style="letter-spacing: 0.5px;">
                        <i class="bi bi-arrow-down-circle me-1"></i> Pengeluaran
                    </h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 small">Kemalangan</span>
                        <span class="fw-bold text-danger-light financial-summary-amount text-break">{{ $formatPengeluaran($summary['kemalangan'] ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 small">Sakit</span>
                        <span class="fw-bold text-danger-light financial-summary-amount text-break">{{ $formatPengeluaran($summary['sakit'] ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 small">Bayar Satpam</span>
                        <span class="fw-bold text-danger-light financial-summary-amount text-break">{{ $formatPengeluaran($summary['bayar_satpam'] ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 small">Konsumsi Rapat</span>
                        <span class="fw-bold text-danger-light financial-summary-amount text-break">{{ $formatPengeluaran($summary['konsumsi_rapat'] ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-white-50 small">Lain-lain</span>
                        <span class="fw-bold text-danger-light financial-summary-amount text-break">{{ $formatPengeluaran($summary['lain_lain'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Column 3: Saldo Akhir -->
            <div class="col-12 col-md-12 col-lg-4">
                <div class="h-100 p-3 rounded-4" style="background: rgba(0, 0, 0, 0.15); border: 1px solid rgba(255, 255, 255, 0.08);">
                    <h6 class="fw-bold text-white text-uppercase mb-3 pb-2 border-bottom border-white border-opacity-25" style="letter-spacing: 0.5px;">
                        <i class="bi bi-bank me-1"></i> Saldo Akhir
                    </h6>
                    
                    <div class="p-3 rounded-3 mb-3" style="background: rgba(255, 255, 255, 0.05);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white-50 small">Saldo Kas RT</span>
                            <span class="fw-bold fs-5 {{ ($summary['saldo_kas'] ?? 0) < 0 ? 'text-danger-light' : 'text-white' }} financial-summary-amount text-break">{{ $formatRupiah($summary['saldo_kas'] ?? 0) }}</span>
                        </div>
                        @if(($summary['saldo_kas'] ?? 0) < 0)
                            <div class="text-warning-light small mt-1" style="font-size: 0.72rem; line-height: 1.2;">
                                <i class="bi bi-exclamation-triangle-fill"></i> Perhatian: saldo bernilai minus. Periksa saldo awal atau transaksi pengeluaran.
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-3 rounded-3 mb-3" style="background: rgba(255, 255, 255, 0.05);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white-50 small">Saldo Keamanan</span>
                            <span class="fw-bold fs-5 {{ ($summary['saldo_keamanan'] ?? 0) < 0 ? 'text-danger-light' : 'text-white' }} financial-summary-amount text-break">{{ $formatRupiah($summary['saldo_keamanan'] ?? 0) }}</span>
                        </div>
                        @if(($summary['saldo_keamanan'] ?? 0) < 0)
                            <div class="text-warning-light small mt-1" style="font-size: 0.72rem; line-height: 1.2;">
                                <i class="bi bi-exclamation-triangle-fill"></i> Perhatian: saldo bernilai minus. Periksa saldo awal atau transaksi pengeluaran.
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.12);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white fw-bold">Saldo Bersih</span>
                            <span class="fw-bold fs-4 {{ ($summary['saldo_bersih'] ?? 0) < 0 ? 'text-danger-light' : 'text-success-light' }} financial-summary-amount text-break">{{ $formatRupiah($summary['saldo_bersih'] ?? 0) }}</span>
                        </div>
                        @if(($summary['saldo_bersih'] ?? 0) < 0)
                            <div class="text-warning-light small mt-1" style="font-size: 0.72rem; line-height: 1.2;">
                                <i class="bi bi-exclamation-triangle-fill"></i> Perhatian: saldo bernilai minus. Periksa saldo awal atau transaksi pengeluaran.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
