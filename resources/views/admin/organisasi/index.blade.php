@extends('layouts.admin')

@section('title', 'Kelola Organisasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Organisasi</h1>
    </div>

    <!-- Search Bar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Cari Organisasi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.organisasi.index') }}" method="GET" class="mb-0">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Cari berdasarkan nama atau email..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Organisasi</h6>
        </div>
        <div class="card-body">
            @if($organisasi->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Organisasi</th>
                                <th>Email</th>
                                <th>Alamat</th>
                                <th>Jumlah Donasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($organisasi as $index => $org)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $org->NAMA_ORGANISASI }}</td>
                                <td>{{ $org->EMAIL_ORGANISASI }}</td>
                                <td>{{ $org->ALAMAT_ORGANISASI }}</td>
                                <td>{{ $org->donasis->count() }}</td>
                                <td>
                                    <a href="{{ route('admin.organisasi.show', $org->ID_ORGANISASI) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.organisasi.edit', $org->ID_ORGANISASI) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.organisasi.destroy', $org->ID_ORGANISASI) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus organisasi ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Links -->
                <div class="mt-3">
                    {{ $organisasi->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-3">
                    <p class="text-muted mb-0">Tidak ada organisasi ditemukan.</p>
                    @if(request('search'))
                        <a href="{{ route('admin.organisasi.index') }}" class="btn btn-sm btn-primary mt-2">Tampilkan Semua</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // DataTable dinonaktifkan karena kita menggunakan Laravel Pagination
    });
</script>
@endsection