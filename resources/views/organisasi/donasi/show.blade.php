@extends('layouts.organisasi')

@section('title', 'Detail Donasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Donasi</h1>
        <a href="{{ route('organisasi.donasi.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Donasi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">ID Donasi</th>
                            <td>{{ $donasi->ID_DONASI }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $donasi->TANGGAL_DONASI ? \Carbon\Carbon::parse($donasi->TANGGAL_DONASI)->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($donasi->barang)
                                    <span class="badge bg-success">Diterima</span>
                                @else
                                    <span class="badge bg-warning text-dark">Request</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Isi Request</th>
                            <td>{{ $donasi->ISI_REQUEST }}</td>
                        </tr>
                        @if($donasi->pegawai)
                        <tr>
                            <th>Diproses Oleh</th>
                            <td>{{ $donasi->pegawai->NAMA_PEGAWAI }}</td>
                        </tr>
                        @endif
                    </table>
                    
                    @if(!$donasi->barang)
                    <div class="mt-3">
                        <a href="{{ route('organisasi.donasi.edit', $donasi->ID_DONASI) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Request
                        </a>
                        <form action="{{ route('organisasi.donasi.destroy', $donasi->ID_DONASI) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus request donasi ini?')">
                                <i class="fas fa-trash"></i> Hapus Request
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        @if($donasi->barang)
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Barang</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">ID Barang</th>
                            <td>{{ $donasi->barang->ID_BARANG }}</td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td>{{ $donasi->barang->NAMA_BARANG }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $donasi->barang->DESKRIPSI ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $donasi->barang->KATEGORI ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Garansi</th>
                            <td>{{ $donasi->barang->GARANSI ?: 'Tidak' }}</td>
                        </tr>
                        @if($donasi->barang->tanggal_garansi)
                        <tr>
                            <th>Tanggal Garansi</th>
                            <td>{{ \Carbon\Carbon::parse($donasi->barang->tanggal_garansi)->format('d M Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if(!$donasi->barang)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Request</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Request Donasi Anda Sedang Diproses</h5>
                        <p>Request donasi Anda telah diterima dan sedang dalam proses review oleh tim ReUseMart. Kami akan segera menghubungi Anda jika request Anda disetujui dan barang donasi siap diserahkan.</p>
                        <hr>
                        <p class="mb-0">Terima kasih atas kesabaran Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Donasi</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h5 class="alert-heading">Donasi Telah Diterima!</h5>
                        <p>Request donasi Anda telah disetujui dan barang donasi telah disiapkan sesuai dengan kebutuhan Anda.</p>
                        <hr>
                        <p class="mb-0">Terima kasih telah menggunakan layanan ReUseMart.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection