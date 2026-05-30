@extends('layouts.app')

@section('title', 'Edit Transaksi - RT.011')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h4 class="fw-bold mb-1" style="color: var(--primary-color);"><i class="bi bi-pencil-square me-2" style="color: var(--secondary-color);"></i>Edit Transaksi</h4>
                <p class="text-muted small mb-0">Ubah rincian nominal, tanggal, atau keterangan transaksi yang sudah disimpan</p>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('payments.update', $payment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <!-- Read-only details -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Kategori Transaksi</label>
                            <input type="text" class="form-control bg-light" value="{{ strtoupper($payment->type) }}" readonly>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Penerima / Pembayar</label>
                            @php
                                $penerima = 'Umum';
                                if ($payment->resident) {
                                    $penerima = $payment->resident->no_rumah . ' - ' . $payment->resident->name;
                                } elseif ($payment->nama_satpam) {
                                    $penerima = $payment->nama_satpam . ' (SATPAM)';
                                }
                            @endphp
                            <input type="text" class="form-control bg-light" value="{{ $penerima }}" readonly>
                        </div>

                        <!-- Amount input -->
                        <div class="col-md-6">
                            <label for="amount" class="form-label small fw-bold">Nominal Transaksi (Rp)</label>
                            <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount', $payment->amount) }}" required>
                        </div>

                        <!-- Date input -->
                        <div class="col-md-6">
                            <label for="date" class="form-label small fw-bold">Tanggal Transaksi</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $payment->date->format('Y-m-d')) }}" required>
                        </div>

                        <!-- Keterangan input -->
                        <div class="col-12">
                            <label for="keterangan" class="form-label small fw-bold">Keterangan / Rincian</label>
                            <textarea name="keterangan" id="keterangan" rows="3" class="form-control" required>{{ old('keterangan', $payment->keterangan) }}</textarea>
                        </div>

                        <!-- Action buttons -->
                        <div class="col-12 mt-4 d-flex justify-content-between">
                            <a href="{{ route('payments.index', ['type' => $payment->type]) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Batal / Kembali
                            </a>
                            <button type="submit" class="btn btn-primary-custom btn-sm">
                                <i class="bi bi-save me-1"></i> Update Transaksi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
