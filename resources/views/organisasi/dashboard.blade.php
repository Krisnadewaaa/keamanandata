@extends('layouts.organisasi')

@section('title', 'Dashboard Organisasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Organisasi</h1>
    </div>

    <div class="row">
        <!-- Total Donasi Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Donasi Diterima</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDonasi }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gift fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Button Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body d-flex justify-content-center align-items-center">
                    <a href="{{ route('organisasi.donasi.create') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-plus-circle me-2"></i> Buat Request Donasi
                    </a>
                </div>
            </div>
        </div>

        <!-- View All Donations Button Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body d-flex justify-content-center align-items-center">
                    <a href="{{ route('organisasi.donasi.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-list me-2"></i> Lihat Semua Donasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Donasi Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Donasi Terbaru</h6>
                </div>
                <div class="card-body">
                    @if($donasiTerbaru->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Barang</th>
                                        <th>Status</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($donasiTerbaru as $donasi)
                                    <tr>
                                        <td>{{ $donasi->TANGGAL_DONASI ? \Carbon\Carbon::parse($donasi->TANGGAL_DONASI)->format('d M Y') : '-' }}</td>
                                        <td>
                                            @if($donasi->barang)
                                                {{ $donasi->barang->NAMA_BARANG }}
                                            @else
                                                <span class="badge bg-warning text-dark">Menunggu</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($donasi->barang)
                                                <span class="badge bg-success">Diterima</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Request</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('organisasi.donasi.show', $donasi->ID_DONASI) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Belum ada donasi.</p>
                            <a href="{{ route('organisasi.donasi.create') }}" class="btn btn-sm btn-success mt-2">
                                <i class="fas fa-plus-circle"></i> Buat Request Donasi
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Organisasi Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Organisasi</h6>
                    <a href="{{ route('organisasi.profile.edit') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Edit Profil
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Nama Organisasi</th>
                                    <td>{{ $organisasi->NAMA_ORGANISASI }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $organisasi->EMAIL_ORGANISASI }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $organisasi->ALAMAT_ORGANISASI }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Petunjuk</h5>
                                <hr>
                                <p class="mb-0">Anda dapat membuat request donasi untuk kebutuhan organisasi Anda. ReUseMart akan meninjau request tersebut dan akan memberikan donasi barang sesuai ketersediaan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection