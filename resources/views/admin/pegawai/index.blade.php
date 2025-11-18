@extends('layouts.admin')

@section('title', 'Kelola Pegawai')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Pegawai</h1>
        <a href="{{ route('admin.pegawai.create') }}" class="btn btn-sm btn-success shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pegawai
        </a>
    </div>

    <!-- Search Bar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Cari Pegawai</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pegawai.index') }}" method="GET" class="mb-0">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Cari berdasarkan nama, email, atau nomor telepon..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pegawai</h6>
        </div>
        <div class="card-body">
            @if($pegawai->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>No. Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pegawai as $index => $p)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $p->NAMA_PEGAWAI }}</td>
                                <td>{{ $p->EMAIL_PEGAWAI }}</td>
                                <td>
                                    @if($p->role)
                                        {{ $p->role->NAMA_ROLE }}
                                    @else
                                        <span class="text-muted">Tidak ada role</span>
                                    @endif
                                </td>
                                <td>{{ $p->NO_TELEPON_PEGAWAI }}</td>
                                <td>
                                    <a href="{{ route('admin.pegawai.show', $p->ID_PEGAWAI) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.pegawai.edit', $p->ID_PEGAWAI) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.pegawai.destroy', $p->ID_PEGAWAI) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
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
                    {{ $pegawai->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-3">
                    <p class="text-muted mb-0">Tidak ada pegawai ditemukan.</p>
                    @if(request('search'))
                        <a href="{{ route('admin.pegawai.index') }}" class="btn btn-sm btn-primary mt-2">Tampilkan Semua</a>
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