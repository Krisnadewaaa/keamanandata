@extends('layouts.app')

@section('title', 'Permintaan Reset Password')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">Permintaan Reset Password</div>
            <div class="card-body">

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @elseif(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="user_type" class="form-label">Tipe Pengguna</label>
                        <select name="user_type" id="user_type" class="form-select" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="pembeli" {{ old('user_type') == 'pembeli' ? 'selected' : '' }}>Pembeli</option>
                            <option value="penitip" {{ old('user_type') == 'penitip' ? 'selected' : '' }}>Penitip</option>
                            <option value="organisasi" {{ old('user_type') == 'organisasi' ? 'selected' : '' }}>Organisasi</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
