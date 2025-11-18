@extends('layouts.organisasi')

@section('title', 'Profil Organisasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Organisasi</h1>
        <a href="{{ route('organisasi.profile.edit') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> Edit Profil
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Organisasi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">ID Organisasi</th>
                            <td>{{ $organisasi->ID_ORGANISASI }}</td>
                        </tr>
                        <tr>
                            <th>Nama Organisasi</th>
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
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Donasi</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Total Donasi:</h5>
                        <h1 class="display-4 text-success">{{ $organisasi->donasis->count() }}</h1>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Status Donasi:</h5>
                        <div class="progress" style="height: 25px;">
                            @php
                                $totalDonasi = $organisasi->donasis->count();
                                $donasiDiterima = $organisasi->donasis->whereNotNull('ID_BARANG')->count();
                                $persenDiterima = $totalDonasi > 0 ? ($donasiDiterima / $totalDonasi) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persenDiterima }}%;" aria-valuenow="{{ $persenDiterima }}" aria-valuemin="0" aria-valuemax="100">
                                {{ round($persenDiterima) }}% Diterima
                            </div>
                        </div>
                        <div class="mt-2 small">
                            <span class="text-success font-weight-bold">{{ $donasiDiterima }}</span> dari {{ $totalDonasi }} request diterima
                        </div>
                    </div>
                    
                    <a href="{{ route('organisasi.donasi.index') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-list me-2"></i> Lihat Semua Donasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection