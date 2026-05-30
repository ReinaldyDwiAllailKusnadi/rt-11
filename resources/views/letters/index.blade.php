@extends('layouts.app')

@section('title', 'Buat Surat RT - RT.011')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-file-earmark-text-fill me-2" style="color: var(--secondary-color);"></i>Pembuatan Surat Keterangan RT</h4>
                <p class="text-muted small mb-0">Cetak surat pengantar keterangan Domisili, Usaha, atau Keterangan Umum warga</p>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('letters.generate') }}" method="POST" target="_blank">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Warga -->
                        <div class="col-md-12">
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

                        <!-- Type of letter -->
                        <div class="col-md-6">
                            <label for="type" class="form-label small fw-bold">Jenis Surat</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="domisili" selected>Surat Keterangan Domisili</option>
                                <option value="usaha">Surat Keterangan Usaha</option>
                                <option value="keterangan">Surat Keterangan Umum</option>
                            </select>
                        </div>

                        <!-- Letter number -->
                        <div class="col-md-6">
                            <label for="nomor" class="form-label small fw-bold">Nomor Surat</label>
                            <input type="text" name="nomor" id="nomor" class="form-control" placeholder="Contoh: 045/RT.011/V/2026" required>
                        </div>

                        <!-- Date -->
                        <div class="col-md-12">
                            <label for="date" class="form-label small fw-bold">Tanggal Surat</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ $today }}" required>
                        </div>

                        <!-- Additional keterangan -->
                        <div class="col-12">
                            <label for="keterangan_tambahan" class="form-label small fw-bold">Keterangan Tambahan / Keperluan (Opsional)</label>
                            <textarea name="keterangan_tambahan" id="keterangan_tambahan" rows="4" class="form-control" placeholder="Tuliskan keterangan tambahan atau peruntukan surat..."></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="col-12 mt-4 text-end">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm me-2">Kembali</a>
                            <button type="submit" class="btn btn-primary-custom btn-sm">
                                <i class="bi bi-file-earmark-richtext me-1"></i> Generate & Preview Surat
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
