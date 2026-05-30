@extends('layouts.app')

@section('title', 'Riwayat Transaksi - RT.011')

@section('content')
<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary-color);"><i class="bi bi-list-ul me-2" style="color: var(--secondary-color);"></i>Riwayat Transaksi RT.011</h4>
            <p class="text-muted mb-0 small">Daftar lengkap riwayat pemasukan dan pengeluaran keuangan</p>
        </div>
        <div>
            <a href="{{ route('payments.create') }}" class="btn btn-secondary-custom btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Input Transaksi Baru
            </a>
        </div>
    </div>
    
    <!-- Filter bar -->
    <div class="card-body px-4 pt-3 pb-1">
        <form action="{{ route('payments.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori --</option>
                    <option value="kas" {{ $type === 'kas' ? 'selected' : '' }}>Kas RT (Iuran)</option>
                    <option value="keamanan" {{ $type === 'keamanan' ? 'selected' : '' }}>Keamanan (Iuran)</option>
                    <option value="kemalangan" {{ $type === 'kemalangan' ? 'selected' : '' }}>Kemalangan (Pengeluaran)</option>
                    <option value="sakit" {{ $type === 'sakit' ? 'selected' : '' }}>Sakit (Pengeluaran)</option>
                    <option value="bayarSATPAM" {{ $type === 'bayarSATPAM' ? 'selected' : '' }}>Bayar Satpam (Pengeluaran)</option>
                    <option value="konsumsiRAPAT" {{ $type === 'konsumsiRAPAT' ? 'selected' : '' }}>Konsumsi Rapat (Pengeluaran)</option>
                    <option value="lainLAIN" {{ $type === 'lainLAIN' ? 'selected' : '' }}>Lain-lain (Pengeluaran)</option>
                </select>
            </div>
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama warga, satpam, keterangan..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 / hal</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 / hal</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 / hal</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 / hal</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom btn-sm w-100"><i class="bi bi-filter"></i> Filter</button>
                @if($type || $search)
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-circle"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table content -->
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light text-muted uppercase small">
                    <tr>
                        <th style="width: 110px;">Tanggal</th>
                        <th>Kategori</th>
                        <th>Nama Penerima/Pembayar</th>
                        <th>No Rumah</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                        <th style="width: 150px;" class="text-center no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $jenisLabels = [
                            'kas' => 'Kas RT',
                            'keamanan' => 'Keamanan',
                            'kemalangan' => 'Kemalangan',
                            'sakit' => 'Sakit',
                            'bayarSATPAM' => 'Bayar Satpam',
                            'konsumsiRAPAT' => 'Konsumsi Rapat',
                            'lainLAIN' => 'Lain-lain'
                        ];
                    @endphp
                    @forelse($payments as $p)
                        <tr>
                            <td>{{ $p->date->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge {{ in_array($p->type, ['kas', 'keamanan']) ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-2 py-1 rounded-pill small">
                                    {{ $jenisLabels[$p->type] ?? $p->type }}
                                </span>
                            </td>
                            <td>
                                @if($p->resident)
                                    <span class="fw-semibold">{{ $p->resident->name }}</span>
                                @elseif($p->nama_satpam)
                                    <span class="fw-semibold">{{ $p->nama_satpam }} (SATPAM)</span>
                                @else
                                    <span class="text-muted">Umum / Kas RT</span>
                                @endif
                            </td>
                            <td>{{ $p->resident ? $p->resident->no_rumah : '-' }}</td>
                            <td class="fw-bold {{ in_array($p->type, ['kas', 'keamanan']) ? 'text-success' : 'text-danger' }}">
                                {{ in_array($p->type, ['kas', 'keamanan']) ? '+' : '-' }} Rp {{ number_format($p->amount, 0, ',', '.') }}
                            </td>
                            <td class="small text-muted">{{ $p->keterangan }}</td>
                            <td class="text-center no-print">
                                <a href="{{ route('payments.edit', $p->id) }}" class="btn btn-outline-secondary btn-sm me-1 rounded-3">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('payments.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini akan mengembalikan saldo.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data transaksi yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted">Menampilkan {{ $payments->firstItem() ?? 0 }} sampai {{ $payments->lastItem() ?? 0 }} dari {{ $payments->total() }} transaksi</span>
            <div>
                {{ $payments->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
