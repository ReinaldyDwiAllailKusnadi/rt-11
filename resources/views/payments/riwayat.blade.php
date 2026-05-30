@extends('layouts.app')

@section('title', 'Rekap Bulanan Warga - RT.011')

@section('content')
<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary-color);"><i class="bi bi-calendar-event-fill me-2" style="color: var(--secondary-color);"></i>Rekap Iuran Bulanan Warga</h4>
            <p class="text-muted mb-0 small">Ringkasan akumulasi pembayaran Kas RT dan Keamanan warga untuk tahun {{ date('Y') }}</p>
        </div>
        <div class="no-print d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer me-1"></i> Cetak</button>
        </div>
    </div>
    
    <div class="card-body px-4 pt-3 pb-1 no-print">
        <form action="{{ route('payments.riwayat') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>Tampilkan 10 warga / hal</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>Tampilkan 25 warga / hal</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>Tampilkan 50 warga / hal</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>Tampilkan 100 warga / hal</option>
                    <option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>Tampilkan Semua Warga</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Table content -->
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light text-muted uppercase small">
                    <tr>
                        <th style="width: 120px;">No Rumah</th>
                        <th>Nama Warga</th>
                        <th>Total Kas RT (Rp)</th>
                        <th>Total Keamanan (Rp)</th>
                        <th>Total Pembayaran (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paginatedResidents as $warga)
                        <tr>
                            <td class="fw-semibold">{{ $warga->no_rumah }}</td>
                            <td>{{ $warga->name }}</td>
                            <td class="fw-medium text-success">Rp {{ number_format($warga->total_kas, 0, ',', '.') }}</td>
                            <td class="fw-medium text-success">Rp {{ number_format($warga->total_keamanan, 0, ',', '.') }}</td>
                            <td class="fw-bold text-dark">Rp {{ number_format($warga->total_kas + $warga->total_keamanan, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($perPage !== 'all')
            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2 no-print">
                <span class="small text-muted">Menampilkan {{ $paginatedResidents->firstItem() ?? 0 }} sampai {{ $paginatedResidents->lastItem() ?? 0 }} dari {{ $paginatedResidents->total() }} warga</span>
                <div>
                    {{ $paginatedResidents->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
