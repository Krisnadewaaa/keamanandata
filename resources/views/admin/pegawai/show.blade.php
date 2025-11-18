@extends('layouts.admin')

@section('title', 'Detail Pegawai')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pegawai</h1>
        <a href="{{ route('admin.pegawai.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pegawai</h6>
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
                                    <span class="text-muted">Tidak ada role</span>
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
                        @if($pegawai->UANG_PEGAWAI)
                        <tr>
                            <th>Saldo Komisi</th>
                            <td>Rp {{ number_format($pegawai->UANG_PEGAWAI, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </table>
                    <div class="mt-3">
                        <a href="{{ route('admin.pegawai.edit', $pegawai->ID_PEGAWAI) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.pegawai.resetPassword', $pegawai->ID_PEGAWAI) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-secondary" onclick="return confirm('Apakah Anda yakin ingin mereset password pegawai ini?')">
                                <i class="fas fa-key"></i> Reset Password
                            </button>
                        </form>
                        <form action="{{ route('admin.pegawai.destroy', $pegawai->ID_PEGAWAI) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @if($pegawai->ID_ROLE == 6) {{-- Jika Hunter --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Barang yang Di-Hunt</h6>
                    </div>
                    <div class="card-body">
                        @php
                            // Kita perlu mengambil barang melalui penitipan
                            $huntedItems = [];
                            foreach($pegawai->barangHunter as $hunterItem) {
                                foreach($hunterItem->penitipan as $penitipan) {
                                    if ($penitipan->barang) {
                                        $huntedItems[] = [
                                            'id_hunter' => $hunterItem->ID_BARANGHUNTER,
                                            'barang' => $penitipan->barang
                                        ];
                                    }
                                }
                            }
                        @endphp
                        
                        @if(count($huntedItems) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>ID Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($huntedItems as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item['barang']->ID_BARANG }}</td>
                                            <td>{{ $item['barang']->NAMA_BARANG }}</td>
                                            <td>{{ $item['barang']->STATUS }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted">Belum ada barang yang di-hunt.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($pegawai->komisi)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Riwayat Komisi</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">ID Komisi</th>
                                <td>{{ $pegawai->komisi->ID_KOMISI }}</td>
                            </tr>
                            <tr>
                                <th>Total Komisi</th>
                                <td>Rp {{ number_format($pegawai->komisi->TOTAL_KOMISI, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Komisi</th>
                                <td>{{ $pegawai->komisi->TANGGAL_KOMISI }}</td>
                            </tr>
                            <tr>
                                <th>Status Komisi</th>
                                <td>{{ $pegawai->komisi->STATUS_KOMISI }}</td>
                            </tr>
                            <tr>
                                <th>Komisi Pegawai</th>
                                <td>Rp {{ number_format($pegawai->komisi->KOMISI_PEGAWAI, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection