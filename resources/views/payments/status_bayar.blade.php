@extends('layouts.app')

@section('title', 'Status Pembayaran Warga - RT.011')

@section('content')
<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary-color);"><i class="bi bi-file-earmark-check-fill me-2" style="color: var(--secondary-color);"></i>Status Pembayaran Warga</h4>
            <p class="text-muted mb-0 small">Monitoring status pelunasan iuran kas & keamanan warga untuk tahun {{ date('Y') }}</p>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer me-1"></i> Cetak Halaman</button>
        </div>
    </div>
    
    <div class="card-body px-4 pt-3 pb-1 no-print">
        <div class="alert alert-info border-0 d-flex align-items-center" role="alert" style="border-radius: 12px;">
            <i class="bi bi-info-circle-fill me-2 fs-5 text-info"></i>
            <div class="small">
                <strong>Informasi:</strong> Bulan berjalan dihitung sebanyak <strong>{{ $jumlahBulan }} bulan</strong> (Januari - {{ now()->locale('id')->isoFormat('MMMM YYYY') }}).
                Target lunas Kas RT: <strong>Rp {{ number_format($targetKasTotal, 0, ',', '.') }}</strong>, Keamanan: <strong>Rp {{ number_format($targetKeamTotal, 0, ',', '.') }}</strong>.
                Baris berwarna <span class="badge bg-danger bg-opacity-10 text-danger px-1">Merah Muda</span> menandakan warga menunggak <strong>lebih dari 3 bulan</strong>.
            </div>
        </div>
    </div>

    <!-- Table content -->
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light text-muted uppercase small">
                    <tr>
                        <th>No Rumah</th>
                        <th>Nama Warga</th>
                        <th>Kas RT (Rp)</th>
                        <th>Status Kas</th>
                        <th>Keamanan (Rp)</th>
                        <th>Status Keamanan</th>
                        <th>Status</th>
                        <th>Tunggakan</th>
                        <th class="text-center no-print">Kwitansi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($residents as $warga)
                        @php
                            $isWarning = $warga->show_warning;
                            $kasStatusText = $warga->total_kas >= $targetKasTotal ? 'Lunas' : ($warga->total_kas > 0 ? 'Sebagian' : 'Belum');
                            $keamStatusText = $warga->total_keamanan >= $targetKeamTotal ? 'Lunas' : ($warga->total_keamanan > 0 ? 'Sebagian' : 'Belum');
                        @endphp
                        <tr class="{{ $isWarning ? 'table-danger table-opacity-25' : '' }}">
                            <td class="fw-semibold">{{ $warga->no_rumah }}</td>
                            <td>{{ $warga->name }}</td>
                            <td>Rp {{ number_format($warga->total_kas, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $kasStatusText === 'Lunas' ? 'badge-lunas' : ($kasStatusText === 'Sebagian' ? 'bg-warning text-dark bg-opacity-25 border border-warning' : 'badge-tunggak') }} px-2 py-1 small">
                                    {{ $kasStatusText }}
                                </span>
                            </td>
                            <td>Rp {{ number_format($warga->total_keamanan, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $keamStatusText === 'Lunas' ? 'badge-lunas' : ($keamStatusText === 'Sebagian' ? 'bg-warning text-dark bg-opacity-25 border border-warning' : 'badge-tunggak') }} px-2 py-1 small">
                                    {{ $keamStatusText }}
                                </span>
                            </td>
                            <td>
                                @if($warga->status_pembayaran === 'LUNAS')
                                    <span class="badge badge-lunas px-2 py-1 rounded-pill small">LUNAS</span>
                                @else
                                    <span class="badge badge-tunggak px-2 py-1 rounded-pill small">BELUM LUNAS</span>
                                @endif
                            </td>
                            <td class="fw-bold {{ $warga->tunggakan > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $warga->tunggakan }} bulan
                            </td>
                            <td class="text-center no-print">
                                <a href="{{ route('receipt.show', $warga->id) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-3">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
