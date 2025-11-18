@extends('layouts.owner')

@section('title', 'Profil Owner')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Owner</h1>
        <a href="{{ route('owner.profile.edit') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> Edit Profil
        </a>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Profil Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pribadi</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Nama</th>
                            <td>{{ $owner->NAMA_PEGAWAI }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $owner->EMAIL_PEGAWAI }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $owner->ALAMAT_PEGAWAI }}</td>
                        </tr>
                        <tr>
                            <th>No. Telepon</th>
                            <td>{{ $owner->NO_TELEPON_PEGAWAI }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Lahir</th>
                            <td>
                                @if($owner->TANGGAL_LAHIR instanceof \DateTime)
                                    {{ $owner->TANGGAL_LAHIR->format('d-m-Y') }}
                                @elseif(is_string($owner->TANGGAL_LAHIR) && !empty($owner->TANGGAL_LAHIR))
                                    {{ date('d-m-Y', strtotime($owner->TANGGAL_LAHIR)) }}
                                @else
                                    Belum diatur
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Role</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Owner</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Pengaturan Password</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="card-title">Gunakan Tanggal Lahir Sebagai Password</h5>
                    <p class="card-text">Ubah password Anda menjadi tanggal lahir dengan format <strong>ddmmyyyy</strong>.</p>
                    @if ($owner->TANGGAL_LAHIR)
                        <form action="{{ route('owner.profile.update-password-dob') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="current_password_dob">Password Saat Ini</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password_dob" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="alert alert-info">
                                Password baru Anda akan menjadi tanggal lahir dengan format: <strong>ddmmyyyy</strong>. 
                                <br>Contoh: <strong>
                                    @if($owner->TANGGAL_LAHIR instanceof \DateTime)
                                        {{ $owner->TANGGAL_LAHIR->format('dmY') }}
                                    @elseif(is_string($owner->TANGGAL_LAHIR) && !empty($owner->TANGGAL_LAHIR))
                                        {{ date('dmY', strtotime($owner->TANGGAL_LAHIR)) }}
                                    @else
                                        01012000
                                    @endif
                                </strong>
                            </div>
                            <button type="submit" class="btn btn-success">Ubah ke Tanggal Lahir</button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            Anda belum mengatur tanggal lahir. Silakan edit profil terlebih dahulu untuk menambahkan tanggal lahir.
                        </div>
                        <a href="{{ route('owner.profile.edit') }}" class="btn btn-warning">Edit Profil</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection