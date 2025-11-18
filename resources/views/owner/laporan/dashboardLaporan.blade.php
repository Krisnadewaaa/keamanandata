@extends('layouts.owner')

@section('title', 'Dashboard Laporan')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4">Dashboard Laporan ReUse Mart</h4>
    
    <div class="row">
        <!-- Laporan Penjualan -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Laporan Penjualan
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Penjualan Bulanan
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Grafik dan tabel penjualan per bulan
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('owner.laporan.penjualan-bulanan') }}" class="btn btn-primary btn-sm" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generate PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laporan Komisi -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Laporan Komisi
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Komisi Bulanan
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Detail komisi per produk per bulan
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('owner.laporan.komisi-bulanan') }}" class="btn btn-success btn-sm" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generate PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laporan Stok Gudang -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Stok Gudang
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Laporan Stok
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Inventory barang saat ini
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('owner.laporan.stok-gudang') }}" class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generate PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laporan Penjualan Kategori -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Penjualan Kategori
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Per Kategori Barang
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Analisis penjualan berdasarkan kategori
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('owner.laporan.penjualan-kategori') }}" class="btn btn-warning btn-sm" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generate PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laporan Masa Titip Habis -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Masa Titip Habis
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Barang Kadaluarsa
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Daftar barang yang masa penitipannya habis
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('owner.laporan.masa-titip-habis') }}" class="btn btn-danger btn-sm" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generate PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laporan Donasi Barang -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Donasi Barang
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Laporan Donasi
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Riwayat donasi barang dengan filter
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('owner.laporan.donasi-barang.filter') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-filter me-1"></i> Filter & Generate
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laporan Request Donasi -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Request Donasi
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Permintaan Donasi
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Request donasi yang belum terpenuhi
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hands-helping fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('owner.laporan.request-donasi.filter') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-filter me-1"></i> Filter & Generate
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laporan Transaksi Penitip -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">
                                Transaksi Penitip
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Laporan per Penitip
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Detail transaksi dan komisi penitip
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('owner.laporan.transaksi-penitip.filter') }}" class="btn btn-purple btn-sm" style="background-color: #6f42c1; border-color: #6f42c1; color: white;">
                            <i class="fas fa-filter me-1"></i> Filter & Generate
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

          
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Laporan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Laporan dengan Filter:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i> Laporan Donasi Barang</li>
                                <li><i class="fas fa-check text-success me-2"></i> Laporan Request Donasi</li>
                                <li><i class="fas fa-check text-success me-2"></i> Laporan Transaksi Penitip</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Laporan Langsung:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-file-pdf text-primary me-2"></i> Penjualan Bulanan</li>
                                <li><i class="fas fa-file-pdf text-primary me-2"></i> Komisi Bulanan</li>
                                <li><i class="fas fa-file-pdf text-primary me-2"></i> Stok Gudang</li>
                                <li><i class="fas fa-file-pdf text-primary me-2"></i> Penjualan Kategori</li>
                                <li><i class="fas fa-file-pdf text-primary me-2"></i> Masa Titip Habis</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection