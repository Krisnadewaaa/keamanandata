@extends('layouts.app')

@section('title', 'Detail Diskusi')

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
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Diskusi</h5>
                        <a href="{{ route('cs.diskusi.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

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

                    <!-- Pertanyaan Original -->
                    <div class="mb-4">
                        <div class="d-flex mb-2">
                            <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="User">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0">{{ $diskusi->NAMA_PENGIRIM }}</h6>
                                    <div class="d-flex">
                                        <span class="text-muted me-3">{{ \Carbon\Carbon::parse($diskusi->created_at)->format('d M Y, H:i') }}</span>
                                        <form action="{{ route('cs.diskusi.destroy', $diskusi->ID_DISKUSI) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus diskusi ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p class="mb-0">{{ $diskusi->KOMENTAR }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Balasan Diskusi -->
                    @if($diskusi->replies->count() > 0)
                        <div class="ms-5 mb-4">
                            @foreach($diskusi->replies as $reply)
                                <div class="d-flex mb-2 p-3 bg-light rounded">
                                    <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Admin">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0">{{ $reply->NAMA_PENGIRIM }} <span class="badge bg-success ms-2">CS</span></h6>
                                            <div class="d-flex">
                                                <span class="text-muted me-3">{{ \Carbon\Carbon::parse($reply->created_at)->format('d M Y, H:i') }}</span>
                                                <form action="{{ route('cs.diskusi.destroy', $reply->ID_DISKUSI) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus balasan ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <p class="mb-0">{{ $reply->KOMENTAR }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Diskusi ini belum memiliki balasan.
                        </div>
                    @endif

                    <!-- Form Balas Diskusi -->
                    <div class="mt-4">
                        <h5 class="mb-3">Balas Diskusi</h5>
                        <form action="{{ route('cs.diskusi.reply', $diskusi->ID_DISKUSI) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control @error('komentar') is-invalid @enderror" id="komentar" name="komentar" rows="3" placeholder="Tulis balasan Anda di sini...">{{ old('komentar') }}</textarea>
                                @error('komentar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Balasan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection