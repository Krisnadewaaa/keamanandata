@extends('layouts.gudang')

@section('title', 'Penjadwalan & Konfirmasi Pengiriman')

@section('content')
@php
    use Carbon\Carbon;
    $now = Carbon::now();
    $jamSekarang = (int) $now->format('H');
@endphp

<div class="container mt-4">
    {{-- Transaksi Menunggu Penjadwalan --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">📦 Transaksi Menunggu Penjadwalan</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-success text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nama Barang</th>
                        <th>Pembeli</th>
                        <th>Tanggal</th>
                        <th>Pengiriman</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksiPending as $transaksi)
                        <tr>
                            <td>{{ $transaksi->ID_TRANSAKSI }}</td>
                            <td>{{ optional($transaksi->barang)->NAMA_BARANG ?? 'Tidak Diketahui' }}</td>
                            <td>{{ optional(optional($transaksi->barang)->pembeli)->NAMA_PEMBELI ?? 'Tidak Diketahui' }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaksi->TANGGAL_TRANSAKSI)->format('d-m-Y') }}</td>
                            <td class="text-center">
                                @if($transaksi->METODE_PENGIRIMAN === 'Ambil Sendiri')
                                    <span class="badge bg-info">Ambil Sendiri</span>
                                @elseif($transaksi->METODE_PENGIRIMAN === 'Kurir')
                                    <span class="badge bg-warning text-dark">Kurir</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $transaksi->STATUS_TRANSAKSI }}</span>
                            </td>
                            <td class="text-center">
                                @if($transaksi->METODE_PENGIRIMAN === 'Kurir' && $jamSekarang >= 16)
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="showToast()">
                                        Konfirmasi Selesai
                                    </button>
                                @else
                                    <form action="{{ route('gudang.transaksi.diterima', $transaksi->ID_TRANSAKSI) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success"
                                        onclick="return confirm('Konfirmasi bahwa barang telah diambil atau dikirim?')">
                                            Konfirmasi Selesai
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada transaksi yang menunggu penjadwalan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        {{-- Penjadwalan Kurir --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">🚚 Penjadwalan - Pengiriman Kurir</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead class="table-light text-center">
                            <tr>
                                <th>ID</th>
                                <th>Barang</th>
                                <th>Pembeli</th>
                                <th>Status</th>
                                <th>Kurir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksiSelesai->where('METODE_PENGIRIMAN', 'Kurir') as $transaksi)
                                <tr>
                                    <td>{{ $transaksi->ID_TRANSAKSI }}</td>
                                    <td>{{ optional($transaksi->barang)->NAMA_BARANG ?? '-' }}</td>
                                    <td>{{ optional(optional($transaksi->barang)->pembeli)->NAMA_PEMBELI ?? '-' }}</td>
                                    <td>
                                        <span class="badge 
                                            {{ $transaksi->STATUS_TRANSAKSI == 'Selesai' ? 'bg-success' : 
                                               ($transaksi->STATUS_TRANSAKSI == 'Sedang Dikirim' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                            {{ $transaksi->STATUS_TRANSAKSI }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $transaksi->kurir->NAMA_PEGAWAI ?? 'Belum Ditugaskan' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('gudang.transaksi.detail', $transaksi->ID_TRANSAKSI) }}" class="btn btn-sm btn-outline-primary">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">Belum ada penjadwalan kurir.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Penjadwalan Ambil Sendiri --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">🧍 Penjadwalan - Ambil Sendiri</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead class="table-light text-center">
                            <tr>
                                <th>ID</th>
                                <th>Barang</th>
                                <th>Pembeli</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksiSelesai->where('METODE_PENGIRIMAN', 'Ambil Sendiri') as $transaksi)
                                <tr>
                                    <td>{{ $transaksi->ID_TRANSAKSI }}</td>
                                    <td>{{ optional($transaksi->barang)->NAMA_BARANG ?? '-' }}</td>
                                    <td>{{ optional(optional($transaksi->barang)->pembeli)->NAMA_PEMBELI ?? '-' }}</td>
                                    <td>
                                        <span class="badge 
                                            {{ $transaksi->STATUS_TRANSAKSI == 'Selesai' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $transaksi->STATUS_TRANSAKSI }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $transaksi->STATUS_TRANSAKSI == 'Selesai' ? 'Barang telah diambil' : 'Belum diambil' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('gudang.transaksi.detail', $transaksi->ID_TRANSAKSI) }}" class="btn btn-sm btn-outline-primary">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">Belum ada penjadwalan ambil sendiri.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast Bootstrap --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="jamToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Tidak bisa melakukan pengiriman lewat kurir setelah jam 16:00.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    function showToast() {
        var toastLive = document.getElementById('jamToast');
        var toast = new bootstrap.Toast(toastLive);
        toast.show();
    }
</script>
@endsection
