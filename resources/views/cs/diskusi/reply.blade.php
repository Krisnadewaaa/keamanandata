@extends('layouts.app')

@section('title', 'Balas Diskusi')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="https://via.placeholder.com/150" alt="Foto Profil" class="img-fluid rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                    <h5>{{ Auth::guard('pegawai')->user()->NAMA_PEGAWAI }}</h5>
                    <p class="text-muted">Customer Service</p>
                </div>
            </div>

            <div class="list-group mt-4">
                <a href="{{ route('cs.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('cs.penitip.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-users me-2"></i> Kelola Penitip
                </a>
                <a href="{{ route('cs.diskusi.index') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-comments me-2"></i> Kelola Diskusi
                </a>
                <a href="{{ route('cs.profile') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-cog me-2"></i> Profil Saya
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Balas Diskusi</h5>
                        <a href="{{ route('cs.diskusi.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <!-- Informasi Produk -->
                    <div class="d-flex align-items-center mb-4">
                        <img src="https://via.placeholder.com/100/28a745/FFFFFF?text={{ substr($diskusi->barang->NAMA_BARANG, 0, 1) }}" class="rounded me-3" alt="{{ $diskusi->barang->NAMA_BARANG }}">
                        <div>
                            <h5 class="mb-1">
                                <!-- PERBAIKAN: Tambahkan parameter ID -->
                                <a href="{{ route('barang.show', ['id' => $diskusi->ID_BARANG]) }}" class="text-decoration-none text-dark">
                                    {{ $diskusi->barang->NAMA_BARANG }}
                                </a>
                            </h5>
                            <p class="text-muted mb-1">{{ Str::limit($diskusi->barang->DESKRIPSI, 100) }}</p>
                            <div class="d-flex align-items-center">
                                <span class="text-success fw-bold me-3">Rp {{ number_format($diskusi->barang->HARGA, 0, ',', '.') }}</span>
                                @if($diskusi->barang->GARANSI == 'Ya')
                                    <span class="badge bg-info text-dark"><i class="fas fa-shield-alt me-1"></i> Garansi</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Anda akan membalas pertanyaan sebagai Customer Service ReUseMart.
                    </div>

                    <!-- Pertanyaan yang akan dibalas -->
                    <div class="mb-4">
                        <div class="d-flex mb-2">
                            <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="User">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0">{{ $diskusi->NAMA_PENGIRIM }}</h6>
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($diskusi->created_at)->format('d M Y, H:i') }}</span>
                                </div>
                                <p class="mb-0">{{ $diskusi->KOMENTAR }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Balas Diskusi -->
                    <form action="{{ route('cs.diskusi.reply', $diskusi->ID_DISKUSI) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="komentar" class="form-label">Balasan Anda</label>
                            <textarea class="form-control @error('komentar') is-invalid @enderror" id="komentar" name="komentar" rows="5" placeholder="Tulis balasan Anda di sini...">{{ old('komentar') }}</textarea>
                            @error('komentar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('cs.diskusi.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Balasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection