@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Ubah Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.reset.submit') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i> Simpan Password Baru
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-white">
                <div class="text-center">
                    <p class="mb-0">Kembali ke <a href="{{ route('login') }}" class="text-decoration-none">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
