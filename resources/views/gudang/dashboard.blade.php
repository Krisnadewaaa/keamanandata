@extends('layouts.gudang')

@section('title', 'Dashboard Pegawai Gudang')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">

            <div class="alert alert-success">
                Selamat datang, <strong>{{ Auth::guard('pegawai')->user()->NAMA_PEGAWAI ?? 'Pegawai Gudang' }}</strong>! Anda login sebagai <strong>Pegawai Gudang</strong>.
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Statistik Stok Barang</h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h5 class="card-title text-muted">Total Barang</h5>
                                    <h3 class="text-success">{{ $totalBarang }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h5 class="card-title text-muted">Barang Stok ≤ 5</h5>
                                    <h3 class="text-warning">{{ $barangMinimum }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Menu Gudang</h4>
                </div>
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <div class="row g-3">

                            <!-- Kelola Stok -->
                            <div class="col-md-4">
                                <div class="card h-100 border-success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-box-open fa-2x text-success mb-2"></i>
                                        <h5 class="card-title">Manajemen Stok</h5>
                                        <a href="{{ route('gudang.stok') }}" class="btn btn-outline-success btn-sm mt-2">Lihat Stok</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Tambah Barang -->
                            <div class="col-md-4">
                                <div class="card h-100 border-success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-plus-circle fa-2x text-success mb-2"></i>
                                        <h5 class="card-title">Tambah Barang</h5>
                                        <a href="{{ route('gudang.stok.tambah') }}" class="btn btn-outline-success btn-sm mt-2">Tambah</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Riwayat Distribusi -->
                            <div class="col-md-4">
                                <div class="card h-100 border-success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-truck fa-2x text-success mb-2"></i>
                                        <h5 class="card-title">Riwayat Distribusi</h5>
                                        <a href="{{ route('gudang.distribusi.riwayat') }}" class="btn btn-outline-success btn-sm mt-2">Lihat Riwayat</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Laporan Distribusi -->
                            <div class="col-md-4">
                                <div class="card h-100 border-success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-file-pdf fa-2x text-success mb-2"></i>
                                        <h5 class="card-title">Cetak Laporan</h5>
                                        <a href="{{ route('gudang.riwayat.distribusi') }}" class="btn btn-outline-success btn-sm mt-2">Lihat Laporan</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Transaksi -->
                            <div class="col-md-4">
                                <div class="card h-100 border-success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clipboard-list fa-2x text-success mb-2"></i>
                                        <h5 class="card-title">Transaksi Pending</h5>
                                        <a href="{{ route('gudang.transaksi.pending') }}" class="btn btn-outline-success btn-sm mt-2">Lihat Daftar</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Transaksi Hangus -->
                            <div class="col-md-4">
                                <div class="card h-100 border-danger">
                                    <div class="card-body text-center">
                                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                                        <h5 class="card-title">Transaksi Hangus</h5>
                                        <a href="{{ route('gudang.transaksi.hangus') }}" class="btn btn-outline-danger btn-sm mt-2">Lihat</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Baru: Riwayat Pengambilan -->
                            <div class="col-md-4">
                                <div class="card h-100 border-info">
                                    <div class="card-body text-center">
                                        <i class="fas fa-history fa-2x text-info mb-2"></i>
                                        <h5 class="card-title">Riwayat Pengambilan</h5>
                                        <a href="{{ route('gudang.pengambilan.riwayat') }}" class="btn btn-outline-info btn-sm mt-2">Lihat Riwayat</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Baru: Jadwalkan Pengiriman -->
                            <div class="col-md-4">
                                <div class="card h-100 border-info">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-alt fa-2x text-info mb-2"></i>
                                        <h5 class="card-title">Penjadwalan & Konfirmasi</h5>
                                        <a href="{{ route('gudang.penjadwalan.index') }}" class="btn btn-outline-info btn-sm mt-2">Buka Menu</a>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- end row -->
                    </div>
                </div>

            </div> <!-- end card shadow-sm -->

            {{-- Contoh list transaksi selesai terbaru --}}
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Transaksi Selesai Terbaru</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse ($pengambilanList as $transaksi)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $transaksi->barang->NAMA_BARANG ?? 'Barang Tidak Diketahui' }}</strong><br>
                                    Status: {{ $transaksi->STATUS_PENITIPAN }} <br>
                                    Tanggal Selesai: {{ \Carbon\Carbon::parse($transaksi->TANGGAL_BERAKHIR)->format('d-m-Y') }}
                                </div>
                                <a href="{{ route('gudang.transaksi.detail', $transaksi->ID_PENITIPAN) }}" class="btn btn-sm btn-primary">Detail</a>
                            </li>
                        @empty
                            <li class="list-group-item">Belum ada transaksi selesai</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
