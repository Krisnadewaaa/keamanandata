@extends('layouts.admin')

@section('title', 'Detail Organisasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Organisasi</h1>
        <a href="{{ route('admin.organisasi.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Organisasi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>ID Organisasi</th>
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
                        <tr>
                            <th>Jumlah Donasi Diterima</th>
                            <td>{{ $organisasi->donasis->count() }}</td>
                        </tr>
                    </table>
                    <div class="mt-3">
                        <a href="{{ route('admin.organisasi.edit', $organisasi->ID_ORGANISASI) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.organisasi.destroy', $organisasi->ID_ORGANISASI) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus organisasi ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Donasi</h6>
                </div>
                <div class="card-body">
                    @if($donasi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Barang</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($donasi as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->TANGGAL_DONASI }}</td>
                                        <td>
                                            @if($item->barang)
                                                {{ $item->barang->NAMA_BARANG }}
                                            @else
                                                <span class="text-muted">Tidak ada data</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->ISI_REQUEST }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted">Belum ada donasi yang diterima</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
