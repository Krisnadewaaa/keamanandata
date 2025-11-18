@extends('layouts.organisasi')

@section('title', 'Edit Profil Organisasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Profil Organisasi</h1>
        <a href="{{ route('organisasi.profile') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="https://via.placeholder.com/150" class="img-thumbnail mb-3" alt="Logo Organisasi" style="width: 150px; height: 150px; object-fit: cover;">
                    <h5>{{ Auth::guard('organisasi')->user()->NAMA_ORGANISASI }}</h5>
                    <p class="text-muted">{{ Auth::guard('organisasi')->user()->EMAIL_ORGANISASI }}</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('organisasi.profile.update') }}" class="btn btn-outline-success">
                            <i class="fas fa-edit me-2"></i> Edit Profil
                        </a>
                        <a href="{{ route('organisasi.password') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-key me-2"></i> Ubah Password
                        </a>
                    </div>
                </div>
            </div>

            <div class="list-group mt-4">
                <a href="{{ route('organisasi.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('organisasi.profile') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user me-2"></i> Profil
                </a>
                <a href="{{ route('organisasi.donasi') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-donate me-2"></i> Donasi
                </a>
            </div>
        </div>

        <!-- Form Edit -->
        <div class="col-lg-9">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Profil</h6>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @elseif (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('organisasi.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nama" class="form-label">Nama Organisasi</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $organisasi->NAMA_ORGANISASI) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $organisasi->EMAIL_ORGANISASI) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" required>{{ old('alamat', $organisasi->ALAMAT_ORGANISASI) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Jika Anda lupa password, silakan reset melalui email menggunakan tombol di bawah ini.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
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
</div>
@endsection
