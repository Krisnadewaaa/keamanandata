@extends('layouts.gudang')

@section('title', 'Stok Barang Gudang')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('gudang.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Stok Barang</li>
    </ol>
</nav>

<h2 class="mb-4">Stok Barang di Gudang</h2>

<div class="card">
    <div class="card-body">
        @if($barang->isEmpty())
            <div class="alert alert-warning text-center">
                <i class="fas fa-box-open me-1"></i> Belum ada barang di gudang.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Garansi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barang as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->NAMA_BARANG }}</td>
                            <td>{{ $item->kategori->JENIS_KATEGORI ?? '-' }}</td>
                            <td>Rp {{ number_format($item->HARGA, 0, ',', '.') }}</td>
                            <td>{{ $item->stok }}</td>
                            <td>
                                <span class="badge bg-{{ $item->STATUS == 'Tersedia' ? 'success' : ($item->STATUS == 'Terjual' ? 'danger' : ($item->STATUS == 'Penitipan' ? 'warning' : 'secondary')) }}">
                                    {{ $item->STATUS }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $item->GARANSI == 'Ya' ? 'info text-dark' : 'light text-muted' }}">
                                    {{ $item->GARANSI }}
                                </span>
                            </td>
                            <td>
                                <!-- Tombol Detail - Tambahkan data-bs-backdrop="static" -->
                                <button class="btn btn-sm btn-info text-white" 
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetail{{ $item->ID_BARANG }}"
                                    data-bs-backdrop="static"
                                    data-bs-keyboard="false">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- Tombol Edit - Tambahkan data-bs-backdrop="static" -->
                                <button class="btn btn-sm btn-warning text-white" 
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit{{ $item->ID_BARANG }}"
                                    data-bs-backdrop="static"
                                    data-bs-keyboard="false">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Tombol Terjual (khusus penitipan) -->
                                @if($item->STATUS === 'Penitipan')
                                <form action="{{ route('gudang.barang.terjual', $item->ID_BARANG) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah barang ini sudah terjual?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success" title="Tandai Terjual">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach 
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- PINDAHKAN SEMUA MODAL KE LUAR TABEL -->
@if(!$barang->isEmpty())
    @foreach($barang as $item)
        <!-- MODAL DETAIL -->
        <div class="modal fade" id="modalDetail{{ $item->ID_BARANG }}" tabindex="-1"
            aria-labelledby="modalDetailLabel{{ $item->ID_BARANG }}" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetailLabel{{ $item->ID_BARANG }}">Detail Barang: {{ $item->NAMA_BARANG }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Kategori:</strong> {{ $item->kategori->JENIS_KATEGORI ?? '-' }}</p>
                                <p><strong>Harga:</strong> Rp {{ number_format($item->HARGA, 0, ',', '.') }}</p>
                                <p><strong>Stok:</strong> {{ $item->stok }}</p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-{{ $item->STATUS == 'Tersedia' ? 'success' : ($item->STATUS == 'Terjual' ? 'danger' : ($item->STATUS == 'Penitipan' ? 'warning' : 'secondary')) }}">
                                        {{ $item->STATUS }}
                                    </span>
                                </p>
                                <p><strong>Garansi:</strong> {{ $item->GARANSI }}</p>
                                <p><strong>Deskripsi:</strong> {{ $item->DESKRIPSI ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <img src="{{ asset('images/fotoProduk/' . ($item->foto_produk ?? 'default.jpg')) }}" 
                                            class="img-thumbnail w-100" 
                                            alt="{{ $item->NAMA_BARANG }}" 
                                            style="height: 150px; object-fit: cover;">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <img src="{{ asset('images/fotoProduk2/' . ($item->foto_produk2 ?? 'default.jpg')) }}" 
                                            class="img-thumbnail w-100" 
                                            alt="{{ $item->NAMA_BARANG }}" 
                                            style="height: 150px; object-fit: cover;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL EDIT -->
        <div class="modal fade" id="modalEdit{{ $item->ID_BARANG }}" tabindex="-1"
            aria-labelledby="modalEditLabel{{ $item->ID_BARANG }}" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('gudang.barang.update', $item->ID_BARANG) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditLabel{{ $item->ID_BARANG }}">Edit Barang: {{ $item->NAMA_BARANG }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Barang</label>
                                        <input type="text" name="nama" class="form-control" value="{{ $item->NAMA_BARANG }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Harga</label>
                                        <input type="number" name="harga" class="form-control" value="{{ $item->HARGA }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stok</label>
                                        <input type="number" name="stok" class="form-control" value="{{ $item->stok }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select" required>
                                            <option value="Tersedia" {{ $item->STATUS == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                            <option value="Terjual" {{ $item->STATUS == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                                            <option value="Donasi" {{ $item->STATUS == 'Donasi' ? 'selected' : '' }}>Donasi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Garansi</label>
                                        <select name="garansi" class="form-select" required>
                                            <option value="Ya" {{ $item->GARANSI == 'Ya' ? 'selected' : '' }}>Ya</option>
                                            <option value="Tidak" {{ $item->GARANSI == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="3">{{ $item->DESKRIPSI }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Foto 1</label>
                                        <input type="file" name="foto1" class="form-control" accept="image/*">
                                        @if($item->foto_produk)
                                            <small class="text-muted">File saat ini: {{ $item->foto_produk }}</small>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Foto 2</label>
                                        <input type="file" name="foto2" class="form-control" accept="image/*">
                                        @if($item->foto_produk2)
                                            <small class="text-muted">File saat ini: {{ $item->foto_produk2 }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

@endsection

@push('scripts')
<script>
// Pastikan modal bersih saat ditutup
document.addEventListener('DOMContentLoaded', function() {
    // Hapus semua backdrop saat modal ditutup
    document.querySelectorAll('.modal').forEach(function(modal) {        
        modal.addEventListener('hidden.bs.modal', function () {
            // Hapus semua backdrop yang mungkin tertinggal
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Kembalikan scroll body
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
        });
        
        // Prevent multiple modal opening
        modal.addEventListener('show.bs.modal', function () {
            // Tutup modal lain yang mungkin terbuka
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(openModal => {
                if (openModal !== modal) {
                    const modalInstance = bootstrap.Modal.getInstance(openModal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
            });
        });
    });
});
</script>
@endpush