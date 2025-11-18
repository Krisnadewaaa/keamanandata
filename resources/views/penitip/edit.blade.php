@extends('layouts.app')

@section('title', 'Edit Profil')

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
                    <h5 class="mb-0">Edit Profil</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('penitip.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="{{ $penitip->NAMA_PENITIP }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $penitip->EMAIL_PENITIP }}" required>
                        </div>

                        <!-- Input untuk Foto Profil -->
                        <div class="mb-3">
                            <label for="foto_profil" class="form-label">Foto Profil</label>
                            <input type="file" class="form-control" id="foto_profil" name="foto_profil" accept="image/*">
                            <small class="text-muted">Hanya format JPG, JPEG, PNG. Maksimal 2MB.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('penitip.profile') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection