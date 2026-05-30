@extends('layouts.app')

@section('title', 'Input Transaksi Baru - RT.011')

@section('content')
<div class="row g-4">
    <!-- Left Form Area -->
    <div class="col-lg-8">
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h4 class="fw-bold mb-1" style="color: var(--primary-color);"><i class="bi bi-plus-circle-fill me-2" style="color: var(--secondary-color);"></i>Input Transaksi Baru</h4>
                <p class="text-muted small mb-0">Masukkan pemasukan iuran warga atau pengeluaran dana RT</p>
            </div>
            
            <div class="card-body px-4 pb-4">
                <!-- Tab Headers -->
                <ul class="nav nav-pills mb-4" id="transactionTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $type === 'kas' || $type === 'keamanan' ? 'active' : '' }}" id="iuran-tab" data-bs-toggle="pill" data-bs-target="#iuranFormPanel" type="button" role="tab" aria-controls="iuranFormPanel" aria-selected="true">
                            <i class="bi bi-people me-1"></i> Iuran Warga
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ in_array($type, ['kemalangan', 'sakit', 'bayarSATPAM', 'konsumsiRAPAT', 'lainLAIN']) ? 'active' : '' }}" id="pengeluaran-tab" data-bs-toggle="pill" data-bs-target="#pengeluaranFormPanel" type="button" role="tab" aria-controls="pengeluaranFormPanel" aria-selected="false">
                            <i class="bi bi-cash-down me-1"></i> Pengeluaran RT
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="transactionTabsContent">
                    
                    <!-- 1. IURAN WARGA FORM PANEL -->
                    <div class="tab-pane fade show {{ $type === 'kas' || $type === 'keamanan' ? 'active' : '' }}" id="iuranFormPanel" role="tabpanel" aria-labelledby="iuran-tab">
                        
                        <!-- Duplicate Warning Block -->
                        @if(session('duplicate_warning'))
                            <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 12px;">
                                <div class="d-flex">
                                    <i class="bi bi-exclamation-triangle-fill fs-4 me-2 text-warning"></i>
                                    <div>
                                        <h6 class="fw-bold text-dark">⚠️ Peringatan Duplikasi Pembayaran!</h6>
                                        <p class="mb-2 text-muted small">{{ session('duplicate_warning') }}</p>
                                        <div class="form-check">
                                            <input type="checkbox" form="iuranForm" name="force_save" id="force_save" value="1" class="form-check-input">
                                            <label class="form-check-label small fw-bold text-dark" for="force_save">
                                                Tetap simpan transaksi ini (duplikasi)?
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('payments.store') }}" method="POST" id="iuranForm">
                            @csrf
                            <input type="hidden" name="force_save_original" value="1"> <!-- Helper to retain values -->

                            <div class="row g-3">
                                <!-- Type Selection (Kas or Keamanan) -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Jenis Iuran</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" id="type_kas" value="kas" {{ $type === 'kas' ? 'checked' : '' }} onchange="updateAmountPerMonth()">
                                            <label class="form-check-label" for="type_kas">Kas RT (Rp 20.000)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" id="type_keamanan" value="keamanan" {{ $type === 'keamanan' ? 'checked' : '' }} onchange="updateAmountPerMonth()">
                                            <label class="form-check-label" for="type_keamanan">Keamanan (Rp 55.000)</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Warga Selection -->
                                <div class="col-md-6">
                                    <label for="resident_id" class="form-label small fw-bold">Pilih Warga</label>
                                    <select name="resident_id" id="resident_id" class="form-select" required>
                                        <option value="">-- Pilih Warga --</option>
                                        @foreach($residents as $warga)
                                            <option value="{{ $warga->id }}" {{ old('resident_id') == $warga->id ? 'selected' : '' }}>
                                                {{ $warga->no_rumah }} - {{ $warga->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Amount per Month & Year -->
                                <div class="col-md-6">
                                    <label for="amount_per_month" class="form-label small fw-bold">Nominal per Bulan (Rp)</label>
                                    <input type="number" name="amount_per_month" id="amount_per_month" class="form-control" value="{{ $type === 'kas' ? $targetKas : $targetKeamanan }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label for="tahun" class="form-label small fw-bold">Tahun</label>
                                    <select name="tahun" id="tahun" class="form-select">
                                        <option value="2026" selected>2026</option>
                                        <option value="2027">2027</option>
                                    </select>
                                </div>

                                <!-- Checklist Bulan -->
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Pilih Bulan Pembayaran</label>
                                    <div class="checkbox-grid">
                                        @php
                                            $bulanList = [
                                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                            ];
                                        @endphp
                                        @foreach($bulanList as $num => $nama)
                                            <label>
                                                <input type="checkbox" name="months[]" value="{{ $num }}" class="checkbox-input month-checkbox" onchange="calculateTotalAmount()" {{ is_array(old('months')) && in_array($num, old('months')) ? 'checked' : '' }}>
                                                <span class="checkbox-btn small">{{ $nama }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Date & Total -->
                                <div class="col-md-6">
                                    <label for="date" class="form-label small fw-bold">Tanggal Bayar</label>
                                    <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $today) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="total_amount_display" class="form-label small fw-bold">Total Pembayaran (Rp)</label>
                                    <input type="text" id="total_amount_display" class="form-control fw-bold text-success fs-5" value="Rp 0" readonly>
                                </div>

                                <!-- Keterangan Custom -->
                                <div class="col-12">
                                    <label for="keterangan" class="form-label small fw-bold">Keterangan Tambahan (Opsional)</label>
                                    <textarea name="keterangan" id="keterangan" rows="2" class="form-control" placeholder="Biarkan kosong untuk keterangan otomatis">{{ old('keterangan') }}</textarea>
                                </div>

                                <!-- Save button -->
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-secondary-custom px-4" id="submitIuranBtn">
                                        <i class="bi bi-save me-1"></i> Simpan Transaksi Iuran
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 2. PENGELUARAN FORM PANEL -->
                    <div class="tab-pane fade show {{ in_array($type, ['kemalangan', 'sakit', 'bayarSATPAM', 'konsumsiRAPAT', 'lainLAIN']) ? 'active' : '' }}" id="pengeluaranFormPanel" role="tabpanel" aria-labelledby="pengeluaran-tab">
                        <form action="{{ route('payments.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <!-- Type Selection -->
                                <div class="col-md-6">
                                    <label for="type_pengeluaran" class="form-label small fw-bold">Kategori Pengeluaran</label>
                                    <select name="type" id="type_pengeluaran" class="form-select" onchange="toggleSatpamField(this.value)" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="kemalangan" {{ old('type') === 'kemalangan' ? 'selected' : '' }}>Kemalangan (mengurangi Saldo Kas)</option>
                                        <option value="sakit" {{ old('type') === 'sakit' ? 'selected' : '' }}>Sakit (mengurangi Saldo Kas)</option>
                                        <option value="bayarSATPAM" {{ old('type') === 'bayarSATPAM' ? 'selected' : '' }}>Bayar Satpam (mengurangi Saldo Keamanan)</option>
                                        <option value="konsumsiRAPAT" {{ old('type') === 'konsumsiRAPAT' ? 'selected' : '' }}>Konsumsi Rapat (mengurangi Saldo Kas)</option>
                                        <option value="lainLAIN" {{ old('type') === 'lainLAIN' ? 'selected' : '' }}>Lain-lain (mengurangi Saldo Kas)</option>
                                    </select>
                                </div>

                                <!-- Warga Penerima (Optional) -->
                                <div class="col-md-6">
                                    <label for="penerima_warga" class="form-label small fw-bold">Penerima Warga (Jika ada)</label>
                                    <select name="resident_id" id="penerima_warga" class="form-select">
                                        <option value="">-- Umum / Non-Warga --</option>
                                        @foreach($residents as $warga)
                                            <option value="{{ $warga->id }}" {{ old('resident_id') == $warga->id ? 'selected' : '' }}>
                                                {{ $warga->no_rumah }} - {{ $warga->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Nama Satpam (Conditional) -->
                                <div class="col-md-12 d-none" id="satpamFieldGroup">
                                    <label for="nama_satpam" class="form-label small fw-bold">Nama Satpam</label>
                                    <input type="text" name="nama_satpam" id="nama_satpam" class="form-control" placeholder="Nama lengkap satpam" value="{{ old('nama_satpam') }}">
                                </div>

                                <!-- Nominal & Tanggal -->
                                <div class="col-md-6">
                                    <label for="amount" class="form-label small fw-bold">Nominal Pengeluaran (Rp)</label>
                                    <input type="number" name="amount" id="amount" class="form-control" placeholder="Contoh: 150000" value="{{ old('amount') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="date_pengeluaran" class="form-label small fw-bold">Tanggal Pengeluaran</label>
                                    <input type="date" name="date" id="date_pengeluaran" class="form-control" value="{{ old('date', $today) }}" required>
                                </div>

                                <!-- Keterangan -->
                                <div class="col-12">
                                    <label for="keterangan_pengeluaran" class="form-label small fw-bold">Keterangan / Rincian Pengeluaran</label>
                                    <textarea name="keterangan" id="keterangan_pengeluaran" rows="3" class="form-control" placeholder="Contoh: Pembelian konsumsi rapat bulanan" required>{{ old('keterangan') }}</textarea>
                                </div>

                                <!-- Save button -->
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary-custom px-4">
                                        <i class="bi bi-save me-1"></i> Simpan Transaksi Pengeluaran
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Info Area -->
    <div class="col-lg-4">
        <!-- Balance Info -->
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-coin me-2" style="color: var(--secondary-color);"></i>Status Saldo Saat Ini</h5>
            </div>
            <div class="card-body px-4 pb-4 pt-1">
                @inject('financeService', 'App\Services\FinanceService')
                <div class="p-3 bg-light rounded-3 mb-3">
                    <span class="text-muted small d-block">SALDO KAS RT</span>
                    <span class="fw-bold fs-5 text-dark">Rp {{ number_format($financeService->getSaldoKas(), 0, ',', '.') }}</span>
                </div>
                <div class="p-3 bg-light rounded-3 mb-3">
                    <span class="text-muted small d-block">SALDO KEAMANAN</span>
                    <span class="fw-bold fs-5 text-dark">Rp {{ number_format($financeService->getSaldoKeamanan(), 0, ',', '.') }}</span>
                </div>
                <div class="p-3 bg-dark text-white rounded-3">
                    <span class="text-white-50 small d-block">TOTAL SALDO GABUNGAN</span>
                    <span class="fw-bold fs-5 text-white">Rp {{ number_format($financeService->getSaldoBersih(), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Payments Log -->
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2" style="color: var(--secondary-color);"></i>Transaksi Terakhir</h5>
            </div>
            <div class="card-body px-4 pb-4 pt-1">
                @forelse($recentPayments as $recent)
                    <div class="border-bottom py-2.5">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge {{ in_array($recent->type, ['kas', 'keamanan']) ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} p-1 px-2 rounded-pill small mb-1" style="font-size: 0.75rem;">
                                    {{ strtoupper($recent->type) }}
                                </span>
                                <p class="mb-0 fw-semibold text-dark small" style="line-height: 1.2;">
                                    {{ $recent->resident ? $recent->resident->name : ($recent->nama_satpam ? $recent->nama_satpam : 'Umum') }}
                                </p>
                                <span class="text-muted small" style="font-size: 0.7rem;">{{ $recent->date->format('d/m/Y') }}</span>
                            </div>
                            <span class="fw-bold small text-dark">
                                {{ in_array($recent->type, ['kas', 'keamanan']) ? '+' : '-' }} Rp {{ number_format($recent->amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small text-center my-3">Belum ada transaksi.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tab switching listener to auto check the type
    document.addEventListener("DOMContentLoaded", function() {
        // Toggle satpam field initially if old value was bayarSATPAM
        const typePengeluaranVal = document.getElementById("type_pengeluaran").value;
        toggleSatpamField(typePengeluaranVal);
        
        // Initial amount and total calculations
        updateAmountPerMonth();
        calculateTotalAmount();
        
        // Show bypass force_save changes button text if warning is active
        @if(session('duplicate_warning'))
            const submitBtn = document.getElementById('submitIuranBtn');
            const forceChk = document.getElementById('force_save');
            
            forceChk.addEventListener('change', function() {
                if (this.checked) {
                    submitBtn.innerHTML = '<i class="bi bi-exclamation-octagon me-1"></i> Paksa Simpan Duplikasi';
                    submitBtn.classList.remove('btn-secondary-custom');
                    submitBtn.classList.add('btn-danger');
                } else {
                    submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Transaksi Iuran';
                    submitBtn.classList.remove('btn-danger');
                    submitBtn.classList.add('btn-secondary-custom');
                }
            });
        @endif
    });

    function updateAmountPerMonth() {
        const isKas = document.getElementById('type_kas').checked;
        const amountInput = document.getElementById('amount_per_month');
        amountInput.value = isKas ? 20000 : 55000;
        calculateTotalAmount();
    }

    function calculateTotalAmount() {
        const amountPerMonth = parseInt(document.getElementById('amount_per_month').value) || 0;
        const checkedCount = document.querySelectorAll('.month-checkbox:checked').length;
        const total = amountPerMonth * checkedCount;
        
        // Format to IDR
        const totalFormatted = "Rp " + total.toLocaleString('id-ID');
        document.getElementById('total_amount_display').value = totalFormatted;
    }

    function toggleSatpamField(val) {
        const satpamGroup = document.getElementById('satpamFieldGroup');
        const satpamInput = document.getElementById('nama_satpam');
        
        if (val === 'bayarSATPAM') {
            satpamGroup.classList.remove('d-none');
            satpamInput.setAttribute('required', 'required');
        } else {
            satpamGroup.classList.add('d-none');
            satpamInput.removeAttribute('required');
        }
    }
</script>
@endsection
