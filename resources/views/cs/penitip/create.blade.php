@extends('layouts.cs')

@section('title', 'Tambah Penitip')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Penitip</h1>
        <a href="{{ route('cs.penitip.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Penitip</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Penting!</h5>
                <p>Pastikan data penitip diisi dengan lengkap dan benar. Foto KTP wajib diunggah dan harus jelas serta dapat dibaca dengan baik.</p>
                <hr>
                <p class="mb-0">Satu nomor KTP hanya dapat digunakan untuk satu akun penitip.</p>
            </div>
            
            <form action="{{ route('cs.penitip.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Penitip <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">Minimal 6 karakter</div>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="no_ktp" class="form-label">Nomor KTP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('no_ktp') is-invalid @enderror" id="no_ktp" name="no_ktp" value="{{ old('no_ktp') }}" required>
                        @error('no_ktp')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">Masukkan 16 digit nomor KTP</div>
                    </div>
                    <div class="col-md-6">
                        <label for="foto_ktp" class="form-label">Foto KTP <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('foto_ktp') is-invalid @enderror" id="foto_ktp" name="foto_ktp" required>
                        @error('foto_ktp')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">Format: JPEG, JPG, PNG. Maks. 2MB</div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Preview KTP image
    document.getElementById('foto_ktp').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = document.createElement('img');
            img.src = event.target.result;
            img.classList.add('img-fluid', 'mt-2', 'border');
            img.style.maxHeight = '200px';
            
            const previewContainer = document.getElementById('preview-container');
            if (!previewContainer) {
                const container = document.createElement('div');
                container.id = 'preview-container';
                container.classList.add('mt-2');
                document.getElementById('foto_ktp').parentNode.appendChild(container);
            }
            
            const container = document.getElementById('preview-container');
            container.innerHTML = '';
            container.appendChild(img);
        }
        reader.readAsDataURL(e.target.files[0]);
    });
</script>
@endsection