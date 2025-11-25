@extends('layouts.app')

@section('title', 'Daftar sebagai Organisasi')

@section('content')
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Daftar sebagai Organisasi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('register.organisasi.submit') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Organisasi</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Organisasi</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            <div class="form-text">Email akan digunakan untuk login dan komunikasi terkait donasi.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat Organisasi</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Dengan mendaftar sebagai organisasi, Anda dapat mengajukan permohonan donasi barang yang tidak terjual di ReUseMart. Kami akan mempertimbangkan permintaan Anda dan menyalurkan barang yang sesuai dengan kebutuhan organisasi Anda.
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya menyetujui 
                                <a href="{{ route('terms') }}" class="text-decoration-none" target="_blank">Syarat dan Ketentuan</a> 
                                serta 
                                <a href="{{ route('privacy') }}" class="text-decoration-none" target="_blank">Kebijakan Privasi</a> 
                                ReUseMart.
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-user-plus me-2"></i> Daftar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-white">
                    <div class="text-center">
                        <p class="mb-0">Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection