@extends('layouts.organisasi')

@section('title', 'Daftar Donasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Donasi</h1>
        <a href="{{ route('organisasi.donasi.create') }}" class="btn btn-sm btn-success shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat Request Donasi
        </a>
    </div>

    <!-- Search Bar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Cari Donasi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('organisasi.donasi.index') }}" method="GET" class="mb-0">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Cari berdasarkan nama barang, tanggal, atau isi request..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Donasi</h6>
        </div>
        <div class="card-body">
            @if($donasi->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Status</th>
                                <th>Isi Request</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donasi as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->TANGGAL_DONASI ? \Carbon\Carbon::parse($item->TANGGAL_DONASI)->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($item->barang)
                                        {{ $item->barang->NAMA_BARANG }}
                                    @else
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->barang)
                                        <span class="badge bg-success">Diterima</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Request</span>
                                    @endif
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($item->ISI_REQUEST, 50) }}</td>
                                <td>
                                    <a href="{{ route('organisasi.donasi.show', $item->ID_DONASI) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(!$item->barang)
                                        <a href="{{ route('organisasi.donasi.edit', $item->ID_DONASI) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('organisasi.donasi.destroy', $item->ID_DONASI) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus request donasi ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Links -->
                <div class="mt-3">
                    {{ $donasi->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-3">
                    <p class="text-muted mb-0">Belum ada donasi ditemukan.</p>
                    @if(request('search'))
                        <a href="{{ route('organisasi.donasi.index') }}" class="btn btn-sm btn-primary mt-2">Tampilkan Semua</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection