@extends('layouts.cs')

@section('title', 'Profil CS')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Customer Service</h1>
        <a href="{{ route('cs.profile.edit') }}" class="btn btn-primary btn-sm">
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Customer Service</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">ID Pegawai</th>
                            <td>{{ $pegawai->ID_PEGAWAI }}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $pegawai->NAMA_PEGAWAI }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $pegawai->EMAIL_PEGAWAI }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>
                                @if($pegawai->role)
                                    {{ $pegawai->role->NAMA_ROLE }}
                                @else
                                    Customer Service
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $pegawai->ALAMAT_PEGAWAI }}</td>
                        </tr>
                        <tr>
                            <th>No. Telepon</th>
                            <td>{{ $pegawai->NO_TELEPON_PEGAWAI }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Lahir</th>
                            <td>
                                @if($pegawai->TANGGAL_LAHIR instanceof \DateTime)
                                    {{ $pegawai->TANGGAL_LAHIR->format('d-m-Y') }}
                                @elseif(is_string($pegawai->TANGGAL_LAHIR) && !empty($pegawai->TANGGAL_LAHIR))
                                    {{ date('d-m-Y', strtotime($pegawai->TANGGAL_LAHIR)) }}
                                @else
                                    Belum diatur
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pengaturan Password</h6>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Gunakan Tanggal Lahir Sebagai Password</h5>
                    <p class="card-text">Ubah password Anda menjadi tanggal lahir dengan format <strong>ddmmyyyy</strong>.</p>
                    @if ($pegawai->TANGGAL_LAHIR)
                        <form action="{{ route('cs.profile.update-password-dob') }}" method="POST">
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
                                    @if($pegawai->TANGGAL_LAHIR instanceof \DateTime)
                                        {{ $pegawai->TANGGAL_LAHIR->format('dmY') }}
                                    @elseif(is_string($pegawai->TANGGAL_LAHIR) && !empty($pegawai->TANGGAL_LAHIR))
                                        {{ date('dmY', strtotime($pegawai->TANGGAL_LAHIR)) }}
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
                        <a href="{{ route('cs.profile.edit') }}" class="btn btn-warning">Edit Profil</a>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Total Penitip:</h5>
                        <h1 class="display-4 text-primary">{{ \App\Models\Penitip::count() }}</h1>
                    </div>
                    
                    <hr>
                    
                    <h5>Tugas Customer Service:</h5>
                    <ul class="list-group list-group-flush mt-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Manajemen Penitip
                            <span class="badge bg-primary rounded-pill">
                                <i class="fas fa-users"></i>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Verifikasi KTP
                            <span class="badge bg-primary rounded-pill">
                                <i class="fas fa-id-card"></i>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pelayanan Pelanggan
                            <span class="badge bg-primary rounded-pill">
                                <i class="fas fa-headset"></i>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Referensi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <a href="{{ route('cs.penitip.index') }}" class="btn btn-block btn-primary mb-2">
                            <i class="fas fa-users me-2"></i> Kelola Penitip
                        </a>
                    </div>
                    <div class="mb-2">
                        <a href="{{ route('cs.penitip.create') }}" class="btn btn-block btn-success mb-2">
                            <i class="fas fa-user-plus me-2"></i> Tambah Penitip
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('home') }}" class="btn btn-block btn-secondary">
                            <i class="fas fa-home me-2"></i> Kembali ke Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection