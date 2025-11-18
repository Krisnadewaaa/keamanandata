@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3 mb-4">
        <div class="card">
            <div class="card-body text-center">
                @if(isset($pembeli))
                    <img src="{{ asset($pembeli->FOTO_PROFIL ? $pembeli->FOTO_PROFIL : 'https://via.placeholder.com/150') }}" alt="Foto Profil" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover; border-radius: 0;">

                    <h5>{{ $pembeli->NAMA_PEMBELI }}</h5>
                    <p class="text-muted">{{ $pembeli->EMAIL_PEMBELI }}</p>
                @endif
                <div class="d-grid gap-2">
                    <a href="{{ route('pembeli.edit') }}" class="btn btn-outline-success">
                        <i class="fas fa-edit me-2"></i> Update Profil
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
            <a href="{{ route('pembeli.alamat.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-map-marker-alt me-2"></i> Alamat
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Edit Profil</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @elseif(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('pembeli.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama"
                            value="{{ old('nama', $pembeli->NAMA_PEMBELI) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email', $pembeli->EMAIL_PEMBELI) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="foto_profil" class="form-label">Foto Profil</label>
                        <input type="file" class="form-control" id="foto_profil" name="foto_profil">
                        @error('foto_profil')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Perubahan pada alamat email akan mempengaruhi login Anda. Pastikan alamat email yang baru dapat diakses.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('pembeli.profile') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
