@extends('layouts.app') {{-- Ganti jika kamu pakai layout lain --}}

@section('content')
<div class="container">
    <h2>Permintaan Reset Password</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.send-link') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" name="email" id="email" class="form-control" required value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label for="tipe" class="form-label">Tipe Pengguna</label>
            <select name="tipe" id="tipe" class="form-control" required>
                <option value="">-- Pilih Tipe --</option>
                <option value="pembeli" {{ old('tipe') == 'pembeli' ? 'selected' : '' }}>Pembeli</option>
                <option value="penitip" {{ old('tipe') == 'penitip' ? 'selected' : '' }}>Penitip</option>
                <option value="organisasi" {{ old('tipe') == 'organisasi' ? 'selected' : '' }}>Organisasi</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
    </form>
</div>
@endsection
