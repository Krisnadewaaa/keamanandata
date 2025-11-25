@extends('layouts.app')

@section('title', 'Verifikasi OTP - Login')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Verifikasi OTP Login</h5>
            </div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.otp.verify') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" 
                               name="email" 
                               class="form-control" 
                               value="{{ session('otp_email') }}" 
                               readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kode OTP</label>
                        <input type="text" class="form-control" name="otp" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">Verifikasi OTP</button>
                    </div>
                </form>

                <form action="{{ route('login.otp.resend') }}" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('otp_email') }}">
                    <button type="submit" class="btn btn-link p-0">
                        Kirim ulang OTP
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
