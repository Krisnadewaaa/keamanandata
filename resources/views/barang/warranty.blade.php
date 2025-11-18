@extends('layouts.app')

@section('title', 'Status Garansi - ' . $barang->NAMA_BARANG)

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('barang.index') }}" class="text-decoration-none">Produk</a></li>
            <li class="breadcrumb-item"><a href="{{ route('barang.show', $barang->ID_BARANG) }}" class="text-decoration-none">{{ $barang->NAMA_BARANG }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Status Garansi</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Status Garansi Produk</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <img src="{{ asset('images/fotoProduk/' . ($barang->foto_produk ?? 'default.jpg')) }}" class="img-thumbnail" alt="{{ $barang->NAMA_BARANG }}">
                        </div>
                        <div class="col-md-9">
                            <h4>{{ $barang->NAMA_BARANG }}</h4>
                            <p class="text-muted">{{ Str::limit($barang->DESKRIPSI, 100) }}</p>
                            <p class="text-success fw-bold">Rp {{ number_format($barang->HARGA, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Warranty Status -->
                    <div class="text-center py-4">
                        @if($warranty['status'] == 'Ya')
                            @if($warranty['under_warranty'])
                                <div class="bg-success bg-opacity-10 p-4 rounded mb-4">
                                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                                    <h3 class="mb-3">Produk Dalam Masa Garansi</h3>
                                    <p class="lead">Produk ini masih dalam masa garansi hingga {{ \Carbon\Carbon::parse($warranty['garansi_date'])->format('d F Y') }}.</p>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Jika terjadi kerusakan atau masalah pada produk, silakan hubungi Customer Service ReUseMart untuk proses klaim garansi.
                                </div>
                            @else
                                <div class="bg-danger bg-opacity-10 p-4 rounded mb-4">
                                    <i class="fas fa-times-circle text-danger fa-4x mb-3"></i>
                                    <h3 class="mb-3">Masa Garansi Telah Berakhir</h3>
                                    @if($warranty['garansi_date'])
                                        <p class="lead">Masa garansi produk ini telah berakhir pada {{ \Carbon\Carbon::parse($warranty['garansi_date'])->format('d F Y') }}.</p>
                                    @else
                                        <p class="lead">Produk ini tidak memiliki informasi tanggal garansi.</p>
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="bg-secondary bg-opacity-10 p-4 rounded mb-4">
                                <i class="fas fa-ban text-secondary fa-4x mb-3"></i>
                                <h3 class="mb-3">Produk Tidak Bergaransi</h3>
                                <p class="lead">Produk ini tidak memiliki garansi.</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Warranty Info -->
                    <div class="mt-4">
                        <h5>Informasi Garansi</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span>Status Garansi</span>
                                <span class="{{ $warranty['status'] == 'Ya' ? 'text-success' : 'text-muted' }}">{{ $warranty['status'] == 'Ya' ? 'Ya' : 'Tidak' }}</span>
                            </li>
                            @if($warranty['status'] == 'Ya')
                                <li class="list-group-item px-0 d-flex justify-content-between">
                                    <span>Tanggal Berakhir</span>
                                    @if($warranty['garansi_date'])
                                        <span class="{{ $warranty['under_warranty'] ? 'text-success' : 'text-danger' }}">{{ \Carbon\Carbon::parse($warranty['garansi_date'])->format('d F Y') }}</span>
                                    @else
                                        <span class="text-muted">Tidak ada informasi</span>
                                    @endif
                                </li>
                                <li class="list-group-item px-0 d-flex justify-content-between">
                                    <span>Status</span>
                                    @if($warranty['under_warranty'])
                                        <span class="text-success">Masih Berlaku</span>
                                    @else
                                        <span class="text-danger">Telah Berakhir</span>
                                    @endif
                                </li>
                                @if($warranty['under_warranty'])
                                    <li class="list-group-item px-0 d-flex justify-content-between">
                                        <span>Sisa Waktu</span>
                                        <span class="text-success">{{ now()->diffInDays($warranty['garansi_date']) }} hari lagi</span>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning mt-4">
                        <i class="fas fa-exclamation-triangle me-2"></i> Perhatian: Garansi hanya berlaku untuk kerusakan yang bukan disebabkan oleh pengguna. Membuka, memodifikasi, atau memperbaiki sendiri produk akan membatalkan garansi.
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('barang.show', $barang->ID_BARANG) }}" class="btn btn-outline-success">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Detail Produk
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-success">
                            <i class="fas fa-headset me-2"></i> Hubungi Customer Service
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection