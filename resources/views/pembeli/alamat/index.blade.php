@extends('layouts.app')
@section('title', 'Alamat Saya')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar on the left -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    @if(isset($pembeli))
                        <img src="{{ asset($pembeli->FOTO_PROFIL ? $pembeli->FOTO_PROFIL : 'https://via.placeholder.com/150') }}" 
                            alt="Foto Profil" 
                            class="img-thumbnail"
                            style="width: 150px; height: 150px; object-fit: cover;">
                        <h5>{{ $pembeli->NAMA_PEMBELI }}</h5>
                        <p class="text-muted">{{ $pembeli->EMAIL_PEMBELI }}</p>
                    @endif
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('pembeli.edit') }}" class="btn btn-outline-success">
                            <i class="fas fa-edit me-2"></i> Edit Profil
                        </a>
                        <a href="{{ route('pembeli.password') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-key me-2"></i> Ubah Password
                        </a>
                    </div>
                </div>
            </div>
                
            <div class="list-group mt-4">
                <a href="{{ route('pembeli.dashboard') }}" class="list-group-item list-group-item-action action">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('pembeli.profile') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user me-2"></i> Profil
                </a>
                <a href="{{ route('pembeli.transactions') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-cart me-2"></i> Riwayat Transaksi
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-heart me-2"></i> Wishlist
                </a>
                <a href="{{ route('pembeli.alamat.index') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-map-marker-alt me-2"></i> Alamat
                </a>
            </div>
        </div>
        
        <!-- Address content on the right -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h4>Daftar Alamat Saya</h4>

                    {{-- Form Pencarian --}}
                    <form method="GET" action="{{ route('pembeli.alamat.index') }}" class="mb-4 row g-2">
                        <div class="col-md-4">
                            <input type="text" name="kota" class="form-control" placeholder="Cari Kota" value="{{ request('kota') }}">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="provinsi" class="form-control" placeholder="Cari Provinsi" value="{{ request('provinsi') }}">
                        </div>
                        <div class="col-md-4 d-flex">
                            <button type="submit" class="btn btn-primary me-2">Cari</button>
                            <a href="{{ route('pembeli.alamat.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>

                    <a href="{{ route('pembeli.alamat.create') }}" class="btn btn-success mb-3">Tambah Alamat</a>

                    <div class="address-list">
                        @forelse($alamat as $item)
                            <div class="card mb-2">
                                <div class="card-body">
                                    <p>{{ $item->ALAMAT_LENGKAP }}, {{ $item->KOTA }}, {{ $item->PROVINSI }}, {{ $item->KODE_POS }}</p>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            @if($item->IS_DEFAULT)
                                                <span class="badge bg-success">Utama</span>
                                            @else
                                                <form action="{{ route('pembeli.alamat.default', $item->ID_ALAMAT) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-primary">Jadikan Utama</button>
                                                </form>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('pembeli.alamat.edit', $item->ID_ALAMAT) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('pembeli.alamat.destroy', $item->ID_ALAMAT) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus alamat?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">Tidak ada alamat ditemukan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection