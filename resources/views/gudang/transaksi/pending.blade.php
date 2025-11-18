@extends('layouts.gudang')

@section('title', 'Daftar Transaksi Pending')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4 fw-bold">
        <i class="fas fa-clock text-success me-2"></i> Daftar Transaksi Pending
    </h3>

    @if($transaksiList->isEmpty())
        <div class="alert alert-info text-center shadow-sm">
            <i class="fas fa-info-circle me-1"></i> Tidak ada transaksi yang perlu dikirim atau diambil.
        </div>
    @else
        <div class="row">
            @foreach ($transaksiList as $transaksi)
                @php
                    $barang = $transaksi->barang;
                    $penitipan = $transaksi->penitipan;

                    $foto1 = $barang->foto_produk ?? null;
                    $foto2 = $barang->foto_produk2 ?? null;

                    $namaPenitip = '-';
                    if ($penitipan) {
                        if ($penitipan->ID_BARANGHUNTER) {
                            $namaPenitip = $penitipan->barangHunter->pegawai->NAMA_PEGAWAI ?? '-';
                        } else {
                            $namaPenitip = $penitipan->penitip->NAMA_PENITIP ?? '-';
                        }
                    }

                    $status = $transaksi->STATUS_TRANSAKSI ?? '-';
                    $tanggalMulai = $transaksi->TANGGAL_TRANSAKSI ?? null;
                @endphp

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow rounded-3 border-0">
                        <div class="card-header bg-success text-white text-center fw-semibold">
                            {{ $barang->NAMA_BARANG ?? 'Barang Tidak Diketahui' }}
                        </div>
                        <div class="card-body">
                            <div class="row g-2 mb-3">
                                @if ($foto1)
                                    <div class="col-6">
                                        <img src="{{ asset('images/fotoProduk/' . $foto1) }}"
                                            class="img-fluid rounded"
                                            alt="Foto Produk 1"
                                            style="width: 100%; height: 160px; object-fit: cover;">
                                    </div>
                                @endif

                                @if ($foto2)
                                    <div class="col-6">
                                        <img src="{{ asset('images/fotoProduk2/' . $foto2) }}"
                                            class="img-fluid rounded"
                                            alt="Foto Produk 2"
                                            style="width: 100%; height: 160px; object-fit: cover;">
                                    </div>
                                @endif

                                @if (!$foto1 && !$foto2)
                                    <div class="col-12">
                                        <img src="{{ asset('images/default-product.jpg') }}"
                                            class="img-fluid rounded"
                                            alt="Foto Default"
                                            style="width: 100%; height: 160px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>

                            <p class="mb-1"><strong>Nama Penitip:</strong> {{ $namaPenitip }}</p>
                            <p class="mb-1"><strong>Status:</strong>
                                <span class="badge bg-warning text-dark">{{ $status }}</span>
                            </p>
                            <p class="mb-3"><strong>Tanggal Transaksi:</strong> 
                                {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->format('d-m-Y') : '-' }}
                            </p>

                            <div class="text-center">
                                <a href="{{ route('gudang.transaksi.show', $transaksi->ID_TRANSAKSI) }}"
                                   class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-search me-1"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $transaksiList->links() }}
        </div>
    @endif
</div>
@endsection
