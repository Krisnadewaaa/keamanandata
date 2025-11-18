@extends('layouts.owner')

@section('title', 'Dashboard Laporan')

@section('styles')
<style>
    :root {
        --primary-green: #28a745; /* A slightly darker, more classic green */
        --light-green: #d4edda; /* For backgrounds or subtle accents */
        --dark-green: #1e7e34; /* For hover states or stronger emphasis */
        --text-color: #343a40;
        --card-bg: #ffffff;
        --card-border: #e0e0e0;
    }

    body {
        background-color: #f8f9fa; /* Light background for the page */
        color: var(--text-color);
    }

    .container {
        padding-top: 30px;
        padding-bottom: 30px;
    }

    /* Style untuk judul dengan background solid */
    .dashboard-title-box {
        background-color: var(--primary-green); /* Warna hijau utama */
        color: #ffffff; /* Teks putih agar kontras */
        padding: 15px 30px; /* Padding yang nyaman */
        border-radius: 10px; /* Sudut sedikit membulat */
        text-align: center;
        margin-bottom: 40px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Sedikit bayangan */
        display: inline-block; /* Agar background hanya selebar teks */
        width: auto; /* Sesuaikan lebar dengan konten */
        margin-left: auto; /* Pusatkan */
        margin-right: auto; /* Pusatkan */
    }

    .dashboard-title-box h2 {
        margin: 0; /* Hapus margin default h2 */
        font-weight: 700;
        font-size: 2.2rem; /* Ukuran font sedikit lebih besar */
    }

    /* Styles kartu dan tombol lainnya tetap sama seperti sebelumnya */
    .card {
        border: 1px solid var(--card-border);
        border-radius: 12px;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        background-color: var(--card-bg);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .card-body {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card-title {
        color: var(--dark-green);
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .card-text {
        color: #6c757d;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        align-self: flex-start;
    }

    .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
        transform: translateY(-2px);
    }

    @media (min-width: 992px) {
        .row-cols-lg-3 > * {
            flex: 0 0 auto;
            width: 33.333333%;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-center"> {{-- Flexbox untuk memusatkan kotak judul --}}
        <div class="dashboard-title-box">
            <h2>Dashboard Laporan Owner</h2>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        {{-- Kartu laporan Anda di sini (sama seperti sebelumnya) --}}
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Penjualan Bulanan</h5>
                    <p class="card-text">Lihat laporan penjualan total untuk bulan ini, memberikan gambaran kinerja finansial bulanan.</p>
                    <a href="{{ route('owner.laporan.penjualan-bulanan') }}" class="btn btn-success">
                        Lihat PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Komisi Bulanan</h5>
                    <p class="card-text">Dapatkan detail laporan komisi yang diperoleh dari setiap produk yang terjual.</p>
                    <a href="{{ route('owner.laporan.komisi-bulanan') }}" class="btn btn-success">
                        Lihat PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Stok Gudang</h5>
                    <p class="card-text">Pantau ketersediaan barang di gudang Anda untuk manajemen inventaris yang efisien.</p>
                    <a href="{{ route('owner.laporan.stok-gudang') }}" class="btn btn-success">
                        Lihat PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Donasi Barang</h5>
                    <p class="card-text">Lihat rekapitulasi barang-barang yang telah didonasikan, termasuk detail penerima dan tanggal.</p>
                    <a href="{{ route('owner.laporan.donasi-barang') }}" class="btn btn-success">
                        Lihat PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Request Donasi</h5>
                    <p class="card-text">Periksa laporan permintaan donasi yang masuk dari berbagai organisasi atau individu.</p>
                    <a href="{{ route('owner.laporan.request-donasi') }}" class="btn btn-success">
                        Lihat PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Penjualan per Kategori</h5>
                    <p class="card-text">Analisis penjualan berdasarkan kategori produk untuk mengidentifikasi tren dan preferensi pelanggan.</p>
                    <a href="{{ route('owner.laporan.penjualan-kategori') }}" class="btn btn-success">
                        Lihat PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Masa Titip Habis</h5>
                    <p class="card-text">Daftar barang dengan masa titip yang akan segera habis, membantu Anda mengelola inventaris penitipan.</p>
                    <a href="{{ route('owner.laporan.masa-titip-habis') }}" class="btn btn-success">
                        Lihat PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Transaksi Penitip</h5>
                    <p class="card-text">Lihat riwayat dan detail semua transaksi yang berkaitan dengan penitipan barang.</p>
                    <a href="{{ route('owner.laporan.transaksi-penitip') }}" class="btn btn-success">
                        Lihat PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection