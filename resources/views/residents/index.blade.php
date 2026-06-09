@extends('layouts.app')

@section('title', 'Kelola Data Warga - RT.011')

@section('content')
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary-color);"><i class="bi bi-people-fill me-2" style="color: var(--secondary-color);"></i>Kelola Data Warga RT.011</h4>
            <p class="text-muted mb-0 small">Tambahkan, edit, atau hapus data warga secara mudah</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Add Warga Trigger -->
            <button type="button" class="btn btn-secondary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addResidentModal">
                <i class="bi bi-person-plus me-1"></i> Tambah Warga
            </button>
            <!-- Reset Warga Trigger -->
            <form action="{{ route('residents.reset') }}" method="POST" onsubmit="return confirm('⚠️ PERINGATAN KERAS!\n\nApakah Anda yakin ingin MERESET data warga ke default?\nTindakan ini akan menghapus SEMUA data warga saat ini beserta SEMUA transaksi iuran yang terkait dengannya.\n\nKlik OK untuk melanjutkan reset.');" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash3 me-1"></i> Reset ke Default
                </button>
            </form>
        </div>
    </div>
    
    <!-- Filter bar -->
    <div class="card-body px-4 pt-3 pb-1">
        <form action="{{ route('residents.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama warga atau nomor rumah..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 Warga / halaman</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 Warga / halaman</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 Warga / halaman</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 Warga / halaman</option>
                </select>
            </div>
            <div class="col-6 col-sm-3 col-md-2">
                <button type="submit" class="btn btn-primary-custom btn-sm w-100"><i class="bi bi-filter me-1"></i>Filter</button>
            </div>
            @if($search)
                <div class="col-6 col-sm-3 col-md-2">
                    <a href="{{ route('residents.index') }}" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-x-circle me-1"></i>Reset</a>
                </div>
            @endif
        </form>
    </div>

    <!-- Table content -->
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light text-muted uppercase small">
                    <tr>
                        <th style="width: 80px;">No Rumah</th>
                        <th>Nama Warga</th>
                        <th style="width: 150px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedResidents as $warga)
                        <tr>
                            <td class="fw-semibold">{{ $warga->no_rumah }}</td>
                            <td>{{ $warga->name }}</td>
                            <td class="text-center">
                                <button 
                                    type="button" 
                                    class="btn btn-outline-secondary btn-sm me-1 rounded-3 btn-edit-resident" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editResidentModal"
                                    data-id="{{ $warga->id }}"
                                    data-name="{{ e($warga->name) }}"
                                    data-no-rumah="{{ e($warga->no_rumah) }}"
                                    data-update-url="{{ route('residents.update', $warga->id) }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <form action="{{ route('residents.destroy', $warga->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus warga ini? Semua transaksi terkait akan ikut terhapus!');">
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
                            <td colspan="3" class="text-center py-4 text-muted">Data warga tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted">Menampilkan {{ $paginatedResidents->firstItem() ?? 0 }} sampai {{ $paginatedResidents->lastItem() ?? 0 }} dari {{ $paginatedResidents->total() }} warga</span>
            <div>
                {{ $paginatedResidents->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Add Resident Modal -->
<div class="modal fade" id="addResidentModal" tabindex="-1" aria-labelledby="addResidentLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addResidentLabel">Tambah Warga Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('residents.store') }}" method="POST">
                @csrf
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label for="no_rumah" class="form-label small fw-bold">Nomor Rumah</label>
                        <input type="text" name="no_rumah" id="no_rumah" class="form-control" placeholder="Contoh: J. 19 atau J19" value="{{ old('no_rumah') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label small fw-bold">Nama Warga</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-secondary-custom btn-sm">Simpan Warga</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Resident Modal -->
<div class="modal fade" id="editResidentModal" tabindex="-1" aria-labelledby="editResidentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editResidentForm" method="POST" class="modal-content" style="border-radius: 16px;">
            @csrf
            @method('PUT')
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editResidentModalLabel">Edit Data Warga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label for="edit_no_rumah" class="form-label small fw-bold">Nomor Rumah</label>
                    <input type="text" name="no_rumah" id="edit_no_rumah" class="form-control" placeholder="Contoh: J. 19 atau J19" required>
                </div>
                <div class="mb-3">
                    <label for="edit_name" class="form-label small fw-bold">Nama Warga</label>
                    <input type="text" name="name" id="edit_name" class="form-control" placeholder="Nama Lengkap" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-custom btn-sm">Update Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
.modal {
    pointer-events: auto;
}
.modal-dialog,
.modal-content,
.modal input,
.modal button {
    pointer-events: auto;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editResidentModal');

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const name = button.getAttribute('data-name') || '';
            const noRumah = button.getAttribute('data-no-rumah') || '';
            const updateUrl = button.getAttribute('data-update-url') || '';

            const form = document.getElementById('editResidentForm');
            const nameInput = document.getElementById('edit_name');
            const noRumahInput = document.getElementById('edit_no_rumah');

            form.setAttribute('action', updateUrl);
            nameInput.value = name;
            noRumahInput.value = noRumah;
        });

        editModal.addEventListener('shown.bs.modal', function () {
            const noRumahInput = document.getElementById('edit_no_rumah');
            if (noRumahInput) {
                noRumahInput.focus();
                noRumahInput.select();
            }
        });
    }
});
</script>
@endsection
