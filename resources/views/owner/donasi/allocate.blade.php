@extends('layouts.owner')

@section('title', 'Alokasi Barang')

@section('content')
<div class="container mt-4">
    <h1>Alokasi Barang untuk Donasi</h1>
    
    {{-- Tampilkan pesan sukses/error jika ada --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($barangDonasi->count() > 0)
        <form action="{{ route('owner.donasi.allocate.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="barang" class="form-label">Pilih Barang <span class="text-muted">({{ $barangDonasi->count() }} tersedia)</span></label>
                        <select class="form-select" name="barang_id" id="barang" required>
                            <option value="">Pilih Barang</option>
                            @foreach($barangDonasi as $barang)
                                <option value="{{ $barang->ID_BARANG }}">
                                    [ID: {{ $barang->ID_BARANG }}] {{ $barang->NAMA_BARANG }}
                                    @if($barang->penitipan && $barang->penitipan->penitip)
                                        - (Penitip: {{ $barang->penitipan->penitip->NAMA_PENITIP }})
                                    @endif
                                    @if($barang->HARGA)
                                        - Rp {{ number_format($barang->HARGA, 0, ',', '.') }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Hanya menampilkan barang dengan status "Tersedia" yang belum pernah didonasikan
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="organisasi" class="form-label">Pilih Organisasi</label>
                        <select class="form-select" name="organisasi_id" id="organisasi" required>
                            <option value="">Pilih Organisasi</option>
                            @foreach($organisasi as $org)
                                <option value="{{ $org->ID_ORGANISASI }}">{{ $org->NAMA_ORGANISASI }}</option>
                            @endforeach
                            @if($organisasi->isEmpty())
                                <option disabled>Tidak ada organisasi tersedia</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-heart me-2"></i>Alokasikan Barang
                </button>
                <a href="{{ route('owner.donasi.requests') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Request
                </a>
            </div>
        </form>

        {{-- Informasi tambahan --}}
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informasi Alokasi Donasi
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Yang Terjadi Setelah Alokasi:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Status barang berubah menjadi "Donasi"</li>
                            <li><i class="fas fa-check text-success me-2"></i>Barang dihubungkan dengan organisasi penerima</li>
                            <li><i class="fas fa-check text-success me-2"></i>Penitip mendapatkan 1 poin reward</li>
                            <li><i class="fas fa-check text-success me-2"></i>Status penitipan berubah menjadi "Donasi"</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Kriteria Barang yang Dapat Didonasikan:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-dot-circle text-primary me-2"></i>Status barang "Tersedia"</li>
                            <li><i class="fas fa-dot-circle text-primary me-2"></i>Belum pernah didonasikan sebelumnya</li>
                            <li><i class="fas fa-dot-circle text-primary me-2"></i>Tidak sedang dalam transaksi</li>
                            <li><i class="fas fa-dot-circle text-primary me-2"></i>Tidak terikat dengan organisasi lain</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <h5 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>Tidak Ada Barang Tersedia
            </h5>
            <p>Saat ini tidak ada barang yang dapat dialokasikan untuk donasi.</p>
            <hr>
            <p class="mb-0">
                <strong>Kemungkinan penyebab:</strong>
                <br>• Semua barang sedang dalam status penitipan aktif
                <br>• Semua barang sudah terjual atau sudah didonasikan
                <br>• Belum ada barang dengan status "Tersedia"
            </p>
            <div class="mt-3">
                <a href="{{ route('owner.donasi.requests') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Request Donasi
                </a>
            </div>
        </div>
    @endif
</div>
@endsection