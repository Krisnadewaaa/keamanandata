@extends('layouts.app')

@section('title', 'Daftar sebagai Pembeli')

@section('content')
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Daftar sebagai Pembeli</h5>
                </div>
                <div class="card-body">
                    {{-- Tampilkan error validasi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Tampilkan pesan sukses --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('register.pembeli.submit') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            <div class="form-text">Email akan digunakan untuk login.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya menyetujui <a href="#" class="text-decoration-none">Syarat dan Ketentuan</a> serta <a href="#" class="text-decoration-none">Kebijakan Privasi</a> ReUseMart.
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
