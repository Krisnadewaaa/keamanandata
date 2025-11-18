@extends('layouts.gudang')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4 text-primary fw-bold">Detail Transaksi ID: {{ $transaksi->ID_TRANSAKSI ?? $transaksi->ID_PENITIPAN }}</h3>

    <div class="row g-4">
        <!-- Barang -->
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header bg-gradient bg-primary text-white rounded-top-4">
                    <i class="fas fa-box-open me-2"></i>Barang
                </div>
                <div class="card-body">
                    <p><strong>Nama:</strong> {{ $transaksi->barang->NAMA_BARANG ?? '-' }}</p>
                    
                    <div class="mb-3">
                        <p class="mb-1 fw-semibold">Foto Produk 1:</p>
                        @if($transaksi->barang->foto_produk)
                            <img src="{{ asset('images/fotoProduk/' . $transaksi->barang->foto_produk) }}" 
                                 alt="Foto Produk 1" 
                                 class="img-fluid rounded shadow-sm border"
                                 style="max-height: 250px; object-fit: cover;">
                        @else
                            <p class="text-muted fst-italic">Tidak ada foto produk 1</p>
                        @endif
                    </div>

                    <div>
                        <p class="mb-1 fw-semibold">Foto Produk 2:</p>
                        @if($transaksi->barang->foto_produk2)
                            <img src="{{ asset('images/fotoProduk2/' . $transaksi->barang->foto_produk2) }}" 
                                 alt="Foto Produk 2" 
                                 class="img-fluid rounded shadow-sm border"
                                 style="max-height: 250px; object-fit: cover;">
                        @else
                            <p class="text-muted fst-italic">Tidak ada foto produk 2</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Penitip -->
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header bg-gradient bg-primary text-white rounded-top-4">
                    <i class="fas fa-user-tag me-2"></i>Penitip
                </div>
                <div class="card-body">
                    @php
                        $penitipName = '-';
                        if ($transaksi->penitipan) {
                            if ($transaksi->penitipan->ID_BARANGHUNTER) {
                                $penitipName = $transaksi->penitipan->barangHunter->pegawai->NAMA_PEGAWAI ?? '-';
                            } else {
                                $penitipName = $transaksi->penitipan->penitip->NAMA_PENITIP ?? '-';
                            }
                        }
                    @endphp
                    <p class="fs-5"><strong>Nama Penitip:</strong> <span class="text-secondary">{{ $penitipName }}</span></p>
                </div>
            </div>

            <!-- Data Pembeli -->
            <div class="card border-0 shadow-lg rounded-4 mt-4">
                <div class="card-header bg-gradient bg-success text-white rounded-top-4">
                    <i class="fas fa-user me-2"></i>Data Pembeli
                </div>
                <div class="card-body">
                    <p class="fs-5"><strong>Nama Pembeli:</strong> <span class="text-secondary">{{ $transaksi->barang->pembeli->NAMA_PEMBELI ?? '-' }}</span></p>
                </div>
            </div>

            <!-- Status & Kurir -->
            <div class="card border-0 shadow-lg rounded-4 mt-4">
                <div class="card-header bg-gradient bg-warning text-dark rounded-top-4">
                    <i class="fas fa-truck me-2"></i>Status & Kurir
                </div>
                <div class="card-body">
                    <p class="fs-5"><strong>Status Transaksi:</strong> <span class="text-secondary">{{ $transaksi->STATUS_TRANSAKSI ?? '-' }}</span></p>

                    @if ($transaksi->STATUS_TRANSAKSI == 'Sedang Dikirim')
                        <p class="fs-5"><strong>Kurir:</strong> 
                            @if ($transaksi->kurir)
                                <span class="text-secondary">{{ $transaksi->kurir->NAMA_PEGAWAI }}</span>
                            @else
                                <span class="text-warning fst-italic">Kurir belum ditentukan</span>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary px-4">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>
@endsection
