@extends('layouts.cs')

@section('title', 'Daftar Penitip')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Penitip</h1>
        <a href="{{ route('cs.penitip.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-user-plus fa-sm text-white-50"></i> Tambah Penitip
        </a>
    </div>

    <!-- Search Bar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Cari Penitip</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('cs.penitip.index') }}" method="GET" class="mb-0">
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
            <h6 class="m-0 font-weight-bold text-primary">Daftar Penitip</h6>
        </div>
        <div class="card-body">
            @if($penitip->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. KTP</th>
                                <th>Rating</th>
                                <th>Saldo</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penitip as $index => $p)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $p->NAMA_PENITIP }}</td>
                                <td>{{ $p->EMAIL_PENITIP }}</td>
                                <td>{{ $p->NO_KTP ?? 'Belum ada' }}</td>
                                <td>
                                    @if($p->RATING_PENITIP)
                                        <div class="d-flex align-items-center">
                                            {{ $p->RATING_PENITIP }}
                                            <div class="ms-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $p->RATING_PENITIP)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @elseif($i - 0.5 <= $p->RATING_PENITIP)
                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Belum ada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($p->UANG_PENITIP)
                                        Rp {{ number_format($p->UANG_PENITIP, 0, ',', '.') }}
                                    @else
                                        Rp 0
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cs.penitip.show', $p->ID_PENITIP) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('cs.penitip.edit', $p->ID_PENITIP) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('cs.penitip.destroy', $p->ID_PENITIP) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus penitip ini?')">
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
                    {{ $penitip->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-3">
                    <p class="text-muted mb-0">Belum ada penitip ditemukan.</p>
                    @if(request('search'))
                        <a href="{{ route('cs.penitip.index') }}" class="btn btn-sm btn-primary mt-2">Tampilkan Semua</a>
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