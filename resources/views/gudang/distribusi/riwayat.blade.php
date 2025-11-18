@extends('layouts.gudang')

@section('title', 'Riwayat Distribusi')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">
            <i class="fas fa-truck-loading me-2 text-success"></i> Riwayat Distribusi
        </h2>
    </div>

    @if($riwayatDistribusi->isEmpty())
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            Belum ada riwayat distribusi yang selesai.
        </div>
    @else
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered text-center mb-0">
                    <thead style="background-color: #28a745; color: white;">
                        <tr class="align-middle">
                            <th scope="col">No</th>
                            <th scope="col">ID Transaksi</th>
                            <th scope="col">Nama Pembeli</th>
                            <th scope="col">Tanggal Selesai</th>
                            <th scope="col">Metode Pembayaran</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatDistribusi as $index => $transaksi)
                        <tr class="align-middle">
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold text-primary">{{ $transaksi->ID_TRANSAKSI }}</td>
                            <td>{{ $transaksi->pembeli->NAMA_PEMBELI ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaksi->TANGGAL_TRANSAKSI)->translatedFormat('d F Y') }}</td>
                            <td>
                                <span class="badge bg-success text-light px-3 py-2 fs-6 text-capitalize">
                                    {{ $transaksi->METODE_PEMBAYARAN }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-dark">Rp {{ number_format($transaksi->TOTAL_TRANSAKSI, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
