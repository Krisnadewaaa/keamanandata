@extends('layouts.app')

@section('title', 'Balas Diskusi - ' . $barang->NAMA_BARANG)

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('barang.index') }}" class="text-decoration-none">Produk</a></li>
            <li class="breadcrumb-item"><a href="{{ route('barang.show', $barang->ID_BARANG) }}" class="text-decoration-none">{{ $barang->NAMA_BARANG }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Balas Diskusi</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Balas Diskusi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Anda akan membalas pertanyaan sebagai CS ReUseMart.
                    </div>

                    <div class="mb-4">
                        <div class="d-flex mb-2">
                            <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="User">
                            <div>
                                <h6 class="mb-0">{{ $diskusi->NAMA_PENGIRIM }}</h6>
                                <small class="text-muted">{{ $diskusi->created_at }}</small>
                            </div>
                        </div>
                        <p class="mb-2">{{ $diskusi->KOMENTAR }}</p>
                    </div>

                    <form action="{{ route('diskusi.reply', $diskusi->ID_DISKUSI) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="komentar" class="form-label">Balasan Anda</label>
                            <textarea class="form-control @error('komentar') is-invalid @enderror" id="komentar" name="komentar" rows="3" placeholder="Tulis balasan Anda di sini...">{{ old('komentar') }}</textarea>
                            @error('komentar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('barang.show', $barang->ID_BARANG) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
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
@endsection