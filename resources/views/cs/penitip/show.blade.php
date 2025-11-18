@extends('layouts.cs')

@section('title', 'Detail Penitip')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Penitip</h1>
        <a href="{{ route('cs.penitip.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Penitip</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">ID Penitip</th>
                            <td>{{ $penitip->ID_PENITIP }}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $penitip->NAMA_PENITIP }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $penitip->EMAIL_PENITIP }}</td>
                        </tr>
                        <tr>
                            <th>No. KTP</th>
                            <td>{{ $penitip->NO_KTP ?? 'Belum ada' }}</td>
                        </tr>
                        <tr>
                            <th>Rating</th>
                            <td>
                                @if($penitip->RATING_PENITIP)
                                    <div class="d-flex align-items-center">
                                        {{ $penitip->RATING_PENITIP }}
                                        <div class="ms-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $penitip->RATING_PENITIP)
                                                    <i class="fas fa-star text-warning"></i>
                                                @elseif($i - 0.5 <= $penitip->RATING_PENITIP)
                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-warning"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Belum ada rating</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Saldo</th>
                            <td>
                                @if($penitip->UANG_PENITIP)
                                    Rp {{ number_format($penitip->UANG_PENITIP, 0, ',', '.') }}
                                @else
                                    Rp 0
                                @endif
                            </td>
                        </tr>
                    </table>
                    <div class="mt-3">
                        <a href="{{ route('cs.penitip.edit', $penitip->ID_PENITIP) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('cs.penitip.destroy', $penitip->ID_PENITIP) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus penitip ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            @if($penitip->FOTO_KTP)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Foto KTP</h6>
                    </div>
                    <div class="card-body text-center">
                        <!-- Mengecek apakah file benar-benar ada di path yang ditentukan -->
                        @if(file_exists(public_path($penitip->FOTO_KTP)) && !empty($penitip->FOTO_KTP))
                            <img src="{{ asset($penitip->FOTO_KTP) }}" 
                                class="img-fluid border" 
                                alt="Foto KTP {{ $penitip->NAMA_PENITIP }}" 
                                style="max-height: 300px;">
                            
                            <div class="mt-2">
                                <a href="{{ asset($penitip->FOTO_KTP) }}" 
                                class="btn btn-sm btn-primary" 
                                target="_blank">
                                    <i class="fas fa-eye"></i> Lihat Full Size
                                </a>
                            </div>
                        @else
                            <p class="text-muted">Foto KTP tidak tersedia.</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Foto KTP</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Belum ada foto KTP yang diunggah
                        </div>
                        <a href="{{ route('cs.penitip.edit', $penitip->ID_PENITIP) }}" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Unggah Foto KTP
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- Riwayat Penitipan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Penitipan</h6>
                </div>
                <div class="card-body">
                    @if($penitip->penitipan()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($penitip->penitipan as $penitipan)
                                        <tr>
                                            <td>{{ $penitipan->ID_PENITIPAN }}</td>
                                            <td>{{ $penitipan->TANGGAL_MULAI ? \Carbon\Carbon::parse($penitipan->TANGGAL_MULAI)->format('d M Y') : '-' }}</td>
                                            <td>
                                                @if($penitipan->STATUS_PENITIPAN == 'Aktif')
                                                    <span class="badge bg-success">Aktif</span>
                                                @elseif($penitipan->STATUS_PENITIPAN == 'Selesai')
                                                    <span class="badge bg-primary">Selesai</span>
                                                @elseif($penitipan->STATUS_PENITIPAN == 'Donasi')
                                                    <span class="badge bg-info">Donasi</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $penitipan->STATUS_PENITIPAN }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Belum ada riwayat penitipan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection