@extends('layouts.cs')

@section('title', 'Edit Penitip')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Penitip</h1>
        <a href="{{ route('cs.penitip.show', $penitip->ID_PENITIP) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Penitip</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('cs.penitip.update', $penitip->ID_PENITIP) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Penitip <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $penitip->NAMA_PENITIP) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $penitip->EMAIL_PENITIP) }}" required>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="no_ktp" class="form-label">Nomor KTP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('no_ktp') is-invalid @enderror" id="no_ktp" name="no_ktp" value="{{ old('no_ktp', $penitip->NO_KTP) }}" required>
                        @error('no_ktp')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">Masukkan 16 digit nomor KTP</div>
                    </div>
                    <div class="col-md-6">
                        <label for="foto_ktp" class="form-label">Foto KTP {{ $penitip->FOTO_KTP ? '(Ganti jika perlu)' : '(Wajib)' }}</label>
                        <input type="file" class="form-control @error('foto_ktp') is-invalid @enderror" id="foto_ktp" name="foto_ktp" {{ $penitip->FOTO_KTP ? '' : 'required' }}>
                        @error('foto_ktp')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">Format: JPEG, JPG, PNG. Maks. 2MB</div>
                        
                        @if($penitip->FOTO_KTP)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $penitip->FOTO_KTP) }}" class="img-fluid border" style="max-height: 150px;" alt="Foto KTP">
                                <div class="form-text">Foto KTP saat ini</div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">Minimal 6 karakter</div>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
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
        if (e.target.files.length > 0) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = document.createElement('img');
                img.src = event.target.result;
                img.classList.add('img-fluid', 'mt-2', 'border');
                img.style.maxHeight = '150px';
                
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
                container.appendChild(document.createElement('div')).classList.add('form-text').textContent = 'Foto KTP baru';
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endsection