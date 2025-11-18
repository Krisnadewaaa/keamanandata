@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Login</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form action="{{ route('login.submit') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="user_type" class="form-label">Masuk Sebagai</label>
                            <select class="form-select" id="user_type" name="user_type" required>
                                <option value="owner" {{ old('user_type') == 'owner' ? 'selected' : '' }}>Owner</option>
                                <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="gudang" {{ old('user_type') == 'gudang' ? 'selected' : '' }}>Pegawai Gudang</option>
                                <option value="customerservice" {{ old('user_type') == 'customerservice' ? 'selected' : '' }}>Customer Service</option>
                                <option value="pembeli" {{ old('user_type') == 'pembeli' ? 'selected' : '' }}>Pembeli</option>
                                <option value="penitip" {{ old('user_type') == 'penitip' ? 'selected' : '' }}>Penitip</option>
                                <option value="organisasi" {{ old('user_type') == 'organisasi' ? 'selected' : '' }}>Organisasi</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ingat Saya</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('password.reset') }}" class="text-decoration-none">Lupa Password?</a>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="text-center">
                        <p class="mb-0">Belum punya akun?</p>
                        <div class="mt-2">
                            <a href="{{ route('register.pembeli') }}" class="btn btn-sm btn-outline-success me-2">Daftar sebagai Pembeli</a>
                            <a href="{{ route('register.organisasi') }}" class="btn btn-sm btn-outline-success">Daftar sebagai Organisasi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection