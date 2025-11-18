@extends('layouts.gudang')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container mt-4">
    <h3 class="text-primary fw-bold mb-4">Detail Transaksi ID: {{ $transaksi->ID_TRANSAKSI }}</h3>

    <div class="row g-4">
        <!-- Foto Produk -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-semibold">Foto Produk</div>
                <div class="card-body">
                    @php
                        $foto1 = $transaksi->barang->foto_produk ?? null;
                        $foto2 = $transaksi->barang->foto_produk2 ?? null;
                    @endphp

                    @if ($foto1)
                        <div class="mb-3">
                            <p class="fw-semibold">Foto Produk 1:</p>
                            <img src="{{ asset('images/fotoProduk/' . $foto1) }}" class="img-thumbnail" style="width: 100%; height: 250px; object-fit: cover;">
                        </div>
                    @endif

                    @if ($foto2)
                        <div>
                            <p class="fw-semibold">Foto Produk 2:</p>
                            <img src="{{ asset('images/fotoProduk2/' . $foto2) }}" class="img-thumbnail" style="width: 100%; height: 250px; object-fit: cover;">
                        </div>
                    @endif

                    @if (!$foto1 && !$foto2)
                        <div>
                            <p class="fw-semibold">Foto Default:</p>
                            <img src="{{ asset('images/default-product.jpg') }}" class="img-thumbnail" style="width: 100%; height: 250px; object-fit: cover;">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detail Barang, Penitip, Pembeli -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white fw-semibold">Detail Transaksi</div>
                <div class="card-body">
                    <p><strong>Nama Barang:</strong> {{ $transaksi->barang->NAMA_BARANG ?? '-' }}</p>
                    <p><strong>Status Transaksi:</strong> {{ $transaksi->STATUS_TRANSAKSI ?? '-' }}</p>
                    <p><strong>Tanggal Transaksi:</strong> {{ $transaksi->TANGGAL_TRANSAKSI ? \Carbon\Carbon::parse($transaksi->TANGGAL_TRANSAKSI)->format('d-m-Y') : '-' }}</p>

                    @php
                        $penitipan = $transaksi->penitipan;
                        $namaPenitip = '-';

                        if ($penitipan) {
                            $namaPenitip = $penitipan->ID_BARANGHUNTER
                                ? ($penitipan->barangHunter->pegawai->NAMA_PEGAWAI ?? '-')
                                : ($penitipan->penitip->NAMA_PENITIP ?? '-');
                        }

                        $namaPembeli = $transaksi->barang->pembeli->NAMA_PEMBELI ?? '-';
                    @endphp

                    <p><strong>Nama Penitip:</strong> {{ $namaPenitip }}</p>
                    <p><strong>Nama Pembeli:</strong> {{ $namaPembeli }}</p>

                    @if ($transaksi->STATUS_TRANSAKSI === 'Sedang Dikirim')
                        <p><strong>Kurir:</strong> 
                            {{ $transaksi->kurir->NAMA_PEGAWAI ?? 'Kurir belum ditentukan' }}
                        </p>
                    @endif
                </div>
            </div>

            <a href="{{ url()->previous() }}" class="btn btn-outline-primary">Kembali</a>
        </div>
    </div>
</div>
@endsection
