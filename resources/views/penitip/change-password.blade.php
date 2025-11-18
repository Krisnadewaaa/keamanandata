@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Profile Picture">
                <h5>{{ Auth::guard('penitip')->user()->NAMA_PENITIP }}</h5>
                <p class="text-muted">{{ Auth::guard('penitip')->user()->EMAIL_PENITIP }}</p>
                <div class="d-flex justify-content-center mb-2">
                    <div class="badge bg-success rounded-pill px-3 py-2">
                        <i class="fas fa-star me-1"></i> Rating: {{ number_format(Auth::guard('penitip')->user()->RATING_PENITIP, 1) }}
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('penitip.edit') }}" class="btn btn-outline-success">
                        <i class="fas fa-edit me-2"></i> Edit Profil
                    </a>
                    <a href="{{ route('penitip.password') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-key me-2"></i> Ubah Password
                    </a>
                </div>
            </div>
        </div>

        <div class="list-group mt-4">
            <a href="{{ route('penitip.profile') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user me-2"></i> Profil
            </a>
            <a href="{{ route('penitip.consignments') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-box me-2"></i> Riwayat Penitipan
            </a>
            {{-- <a href="{{ route('penitip.sales') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-chart-line me-2"></i> Riwayat Penjualan
            </a> --}}
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

                <form action="{{ route('penitip.password.update') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        @error('current_password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Password minimal 6 karakter.</div>
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Pastikan Anda mengingat password baru Anda. Jika Anda lupa password, Anda perlu melakukan reset password melalui email.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-key me-2"></i> Ubah Password
                        </button>
                        <a href="{{ route('penitip.profile') }}" class="btn btn-outline-secondary">
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
