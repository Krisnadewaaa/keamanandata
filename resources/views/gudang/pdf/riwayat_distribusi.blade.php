@extends('layouts.gudang')

@section('content')
<div class="container py-4">
    <h2 class="h4 fw-bold mb-4">
        <i class="fas fa-clipboard-list me-2 text-success"></i> Riwayat Distribusi & Cetak Nota
    </h2>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center mb-0">
                    <thead style="background-color: #198754; color: white;">
                        <tr class="align-middle">
                            <th scope="col">No</th>
                            <th scope="col">ID Transaksi</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Pembeli</th>
                            <th scope="col">Metode Distribusi</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiList as $index => $transaksi)
                        <tr class="align-middle">
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold text-primary">{{ $transaksi->ID_TRANSAKSI }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaksi->TANGGAL_TRANSAKSI)->format('d-m-Y') }}</td>
                            <td>{{ $transaksi->pembeli->NAMA_PEMBELI ?? '-' }}</td>
                            <td>
                                @if($transaksi->METODE_PENGIRIMAN === 'Kurir')
                                    <span class="badge bg-primary px-3 py-2">Diantar Kurir</span>
                                @elseif($transaksi->METODE_PENGIRIMAN === 'Ambil Sendiri')
                                    <span class="badge bg-success px-3 py-2">Diambil Pembeli</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($transaksi->METODE_PENGIRIMAN === 'Kurir')
                                    <a href="{{ route('gudang.nota.kurir', $transaksi->ID_TRANSAKSI) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-invoice me-1"></i> Nota Kurir
                                    </a>
                                @elseif($transaksi->METODE_PENGIRIMAN === 'Ambil Sendiri')
                                    <a href="{{ route('gudang.nota.ambil', $transaksi->ID_TRANSAKSI) }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-receipt me-1"></i> Nota Pembeli
                                    </a>
                                @else
                                    <span class="text-muted">Tidak Diketahui</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle me-1"></i> Tidak ada transaksi tersedia.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
