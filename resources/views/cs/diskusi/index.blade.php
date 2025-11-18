@extends('layouts.app')

@section('title', 'Kelola Diskusi')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="https://via.placeholder.com/150" alt="Foto Profil" class="img-fluid rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                    <h5>{{ Auth::guard('pegawai')->user()->NAMA_PEGAWAI }}</h5>
                    <p class="text-muted">Customer Service</p>
                </div>
            </div>

            <div class="list-group mt-4">
                <a href="{{ route('cs.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('cs.penitip.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-users me-2"></i> Kelola Penitip
                </a>
                <a href="{{ route('cs.diskusi.index') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-comments me-2"></i> Kelola Diskusi
                </a>
                <a href="{{ route('cs.profile') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-cog me-2"></i> Profil Saya
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Kelola Diskusi</h5>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form action="{{ route('cs.diskusi.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Cari diskusi..." name="search" value="{{ request('search') }}">
                                    <button class="btn btn-outline-success" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" name="status" onchange="this.form.submit()">
                                    <option value="">-- Semua Status --</option>
                                    <option value="answered" {{ request('status') == 'answered' ? 'selected' : '' }}>Sudah Dijawab</option>
                                    <option value="unanswered" {{ request('status') == 'unanswered' ? 'selected' : '' }}>Belum Dijawab</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('cs.diskusi.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    @if($diskusi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Pengirim</th>
                                        <th>Pertanyaan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($diskusi as $item)
                                        <tr>
                                            <td>
                                                @if($item->barang)
                                                    <!-- PERBAIKAN: Tambahkan parameter ID -->
                                                    <a href="{{ route('barang.show', ['id' => $item->ID_BARANG]) }}" class="text-decoration-none">
                                                        {{ Str::limit($item->barang->NAMA_BARANG, 20) }}
                                                    </a>
                                                @else
                                                    <span class="text-danger">Barang tidak tersedia</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->NAMA_PENGIRIM }}</td>
                                            <td>{{ Str::limit($item->KOMENTAR, 30) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($item->replies->count() > 0)
                                                    <span class="badge bg-success">Sudah Dijawab</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Belum Dijawab</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('cs.diskusi.show', $item->ID_DISKUSI) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('cs.diskusi.reply', $item->ID_DISKUSI) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-reply"></i>
                                                    </a>
                                                    <form action="{{ route('cs.diskusi.destroy', $item->ID_DISKUSI) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus diskusi ini?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $diskusi->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                            <p class="lead">Tidak ada diskusi yang ditemukan.</p>
                            <p class="text-muted">Mungkin Anda perlu mengubah pencarian atau mengatur ulang filter.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection