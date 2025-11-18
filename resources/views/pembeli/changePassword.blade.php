@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3 mb-4">
        <div class="card">
            <div class="card-body text-center">
                @if(isset($pembeli))
                    <img src="{{ $pembeli->FOTO_PROFIL ? asset($pembeli->FOTO_PROFIL) : 'https://via.placeholder.com/150' }}" alt="Foto Profil" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover; border-radius: 0;">
                    <h5>{{ $pembeli->NAMA_PEMBELI }}</h5>
                    <p class="text-muted">{{ $pembeli->EMAIL_PEMBELI }}</p>
                @endif
                <div class="d-grid gap-2">
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
            <a href="{{ route('pembeli.dashboard') }}" class="list-group-item list-group-item-action">
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
                <h5 class="mb-0">Ubah Password</h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @elseif (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('pembeli.password.update') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        @error('current_password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <div class="form-text">Password minimal 6 karakter.</div>
                        @error('new_password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Pastikan Anda mengingat password baru Anda. Jika lupa, Anda harus reset via email.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-key me-2"></i> Ubah Password
                        </button>
                        <a href="{{ route('pembeli.profile') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i> Batal
                        </a>
                    </div>
                </form>

                <hr>

                <!-- Reset Password via Email -->
                <div class="text-center mt-4">
                    <p class="text-muted">Atau reset password melalui email:</p>
                    <a href="{{ route('password.request') }}" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i> Kirim Link Reset Password ke Email
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
