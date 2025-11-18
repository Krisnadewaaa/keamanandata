@extends('layouts.gudang')

@section('title', 'Daftar Penitipan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Penitipan</h1>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pencarian</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('gudang.penitipan.search') }}">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari berdasarkan kode penitipan, nama penitip, atau nama barang...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <a href="{{ route('gudang.penitipan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Penitipan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $penitipan->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Penitipan Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $penitipan->where('STATUS_PENITIPAN', 'Aktif')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Akan Berakhir (7 hari)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $penitipan->where('STATUS_PENITIPAN', 'Aktif')
                                             ->filter(function($item) {
                                                 if (!$item->TANGGAL_BERAKHIR) return false;
                                                 $sisaHari = \Carbon\Carbon::parse($item->TANGGAL_BERAKHIR)->diffInDays(now(), false);
                                                 return $sisaHari >= 0 && $sisaHari <= 7;
                                             })->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Selesai & Donasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $penitipan->whereIn('STATUS_PENITIPAN', ['Selesai', 'Donasi'])->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Penitipan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Penitipan</th>
                            <th>Penitip</th>
                            <th>Barang</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Berakhir</th>
                            <th>Status Penitipan</th>
                            <th>Sisa Hari</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penitipan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>PNT-{{ str_pad($item->ID_PENITIPAN, 4, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td>
                                @if($item->penitip)
                                    <div class="font-weight-bold">{{ $item->penitip->NAMA_PENITIP }}</div>
                                @else
                                    <div class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Data penitip tidak ditemukan
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($item->barang)
                                    <div class="font-weight-bold">{{ $item->barang->NAMA_BARANG }}</div>
                                @else
                                    <div class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Data barang tidak ditemukan
                                    </div>
                                @endif
                            </td>
                            <td>
                                {{ $item->TANGGAL_MULAI ? \Carbon\Carbon::parse($item->TANGGAL_MULAI)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td>
                                {{ $item->TANGGAL_BERAKHIR ? \Carbon\Carbon::parse($item->TANGGAL_BERAKHIR)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td>
                               {{ $item->STATUS_PENITIPAN }}
                            </td>
                            <td>
                                @if($item->STATUS_PENITIPAN == 'Aktif' && $item->TANGGAL_BERAKHIR)
                                    @php
                                        $tanggalBerakhir = \Carbon\Carbon::parse($item->TANGGAL_BERAKHIR)->endOfDay();
                                        $sekarang = \Carbon\Carbon::now();
                                        $sisaHari = $sekarang->diffInDays($tanggalBerakhir, false);
                                    @endphp
                                    @if($sisaHari < 0)
                                        <span class="text-danger font-weight-bold">
                                            <i class="fas fa-exclamation-circle"></i> {{ substr((string) abs($sisaHari), 0, 2) }} hari terlambat
                                        </span>
                                    @elseif($sisaHari == 0)
                                        <span class="text-warning font-weight-bold">
                                            <i class="fas fa-clock"></i> Berakhir hari ini
                                        </span>
                                    @elseif($sisaHari <= 7)
                                        <span class="text-warning font-weight-bold">
                                            <i class="fas fa-clock"></i> {{ substr((string) $sisaHari, 0, 2) }} hari lagi
                                        </span>
                                    @else
                                        <span class="text-success">
                                            {{ substr((string) $sisaHari, 0, 2) }} hari lagi
                                        </span>
                                    @endif

                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <!-- Button Detail -->
                                    <button type="button" class="btn btn-info btn-sm btn-detail" 
                                            data-toggle="modal" 
                                            data-target="#detailModal{{ $item->ID_PENITIPAN }}"
                                            data-id="{{ $item->ID_PENITIPAN }}"
                                            title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- Button Update -->
                                    <button type="button" class="btn btn-warning btn-sm" 
                                            data-toggle="modal" 
                                            data-target="#updateModal{{ $item->ID_PENITIPAN }}"
                                            title="Update Penitipan">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data penitipan yang ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Update Penitipan - DIPERBAIKI TANPA LOADING --}}
@foreach($penitipan as $item)
<div class="modal fade" id="updateModal{{ $item->ID_PENITIPAN }}" tabindex="-1" role="dialog" 
     aria-labelledby="updateModalLabel{{ $item->ID_PENITIPAN }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="updateModalLabel{{ $item->ID_PENITIPAN }}">
                    <i class="fas fa-edit"></i> Update Penitipan PNT-{{ str_pad($item->ID_PENITIPAN, 4, '0', STR_PAD_LEFT) }}
                </h5>
            </div>
            <form action="{{ route('gudang.penitipan.update', $item->ID_PENITIPAN) }}" method="POST" 
                  enctype="multipart/form-data" id="updateForm{{ $item->ID_PENITIPAN }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- Alert untuk error --}}
                    <div id="errorAlert{{ $item->ID_PENITIPAN }}" class="alert alert-danger" style="display: none;">
                        <ul id="errorList{{ $item->ID_PENITIPAN }}"></ul>
                    </div>

                    <div class="row">
                        {{-- Informasi Penitipan --}}
                        <div class="col-md-6">
                            <div class="card border-left-primary mb-3">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-primary mb-3">
                                        <i class="fas fa-handshake"></i> Informasi Penitipan
                                    </h6>
                                    
                                    <div class="form-group">
                                        <label for="ID_PENITIP{{ $item->ID_PENITIPAN }}" class="font-weight-bold">
                                            Penitip: <span class="text-danger">*</span>
                                        </label>
                                        <select name="ID_PENITIP" id="ID_PENITIP{{ $item->ID_PENITIPAN }}" 
                                                class="form-control" required>
                                            <option value="">Pilih Penitip</option>
                                            @if(isset($penitips) && count($penitips) > 0)
                                                @foreach($penitips as $penitip)
                                                    <option value="{{ $penitip->ID_PENITIP }}" 
                                                        {{ $item->ID_PENITIP == $penitip->ID_PENITIP ? 'selected' : '' }}>
                                                        {{ $penitip->NAMA_PENITIP }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">Data penitip tidak tersedia</option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="TANGGAL_MULAI{{ $item->ID_PENITIPAN }}" class="font-weight-bold">
                                            Tanggal Mulai: <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="TANGGAL_MULAI" id="TANGGAL_MULAI{{ $item->ID_PENITIPAN }}" 
                                               class="form-control" value="{{ $item->TANGGAL_MULAI }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="TANGGAL_BERAKHIR{{ $item->ID_PENITIPAN }}" class="font-weight-bold">
                                            Tanggal Berakhir: <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="TANGGAL_BERAKHIR" id="TANGGAL_BERAKHIR{{ $item->ID_PENITIPAN }}" 
                                               class="form-control" value="{{ $item->TANGGAL_BERAKHIR }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="STATUS_PENITIPAN{{ $item->ID_PENITIPAN }}" class="font-weight-bold">
                                            Status Penitipan: <span class="text-danger">*</span>
                                        </label>
                                        <select name="STATUS_PENITIPAN" id="STATUS_PENITIPAN{{ $item->ID_PENITIPAN }}" 
                                                class="form-control" required>
                                            <option value="Aktif" {{ $item->STATUS_PENITIPAN == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="Selesai" {{ $item->STATUS_PENITIPAN == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="Donasi" {{ $item->STATUS_PENITIPAN == 'Donasi' ? 'selected' : '' }}>Donasi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Informasi Barang dan Upload Foto --}}
                        <div class="col-md-6">
                            <div class="card border-left-warning">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-warning mb-3">
                                        <i class="fas fa-box"></i> Informasi Barang
                                    </h6>
                                    
                                    {{-- Dropdown barang --}}
                                    <div class="form-group">
                                        <label for="id_barang{{ $item->ID_PENITIPAN }}" class="font-weight-bold">
                                            Barang: <span class="text-danger">*</span>
                                        </label>
                                        <select name="id_barang" id="id_barang{{ $item->ID_PENITIPAN }}" 
                                                class="form-control" required>
                                            <option value="">Pilih Barang</option>
                                            @if(isset($barangs) && count($barangs) > 0)
                                                @foreach($barangs as $barang)
                                                    @php
                                                        // Tampilkan barang yang tersedia atau barang yang sedang digunakan penitipan ini
                                                        $canSelect = ($barang->STATUS == 'Tersedia') || 
                                                                   ($barang->ID_PENITIPAN == $item->ID_PENITIPAN);
                                                    @endphp
                                                    @if($canSelect)
                                                        <option value="{{ $barang->ID_BARANG }}" 
                                                            {{ ($item->barang && $item->barang->ID_BARANG == $barang->ID_BARANG) ? 'selected' : '' }}>
                                                            {{ $barang->NAMA_BARANG }} 
                                                            ({{ $barang->kategori->NAMA_KATEGORI ?? 'N/A' }}) 
                                                            - Rp {{ number_format($barang->HARGA, 0, ',', '.') }}
                                                            @if($barang->STATUS == 'Penitipan' && $barang->ID_PENITIPAN == $item->ID_PENITIPAN)
                                                                <em>(Sedang digunakan)</em>
                                                            @endif
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @else
                                                <option value="">Data barang tidak tersedia</option>
                                            @endif
                                        </select>
                                        <small class="text-muted">Hanya barang yang tersedia atau barang penitipan ini yang ditampilkan</small>
                                    </div>

                                    {{-- Upload Foto Produk 1 --}}
                                    <div class="form-group">
                                        <label class="font-weight-bold">Foto Produk 1:</label>
                                        @if($item->barang && $item->barang->foto_produk)
                                            <div class="mb-2">
                                                <img src="{{ asset('images/fotoProduk/' . $item->barang->foto_produk) }}" 
                                                     class="img-thumbnail" style="max-width: 100px; max-height: 100px;"
                                                     alt="Foto Produk 1" id="preview1_{{ $item->ID_PENITIPAN }}">
                                                <small class="text-muted d-block">Foto saat ini</small>
                                            </div>
                                        @endif
                                        <input type="file" name="foto_produk" class="form-control-file" 
                                               accept="image/jpeg,image/png,image/jpg"
                                               onchange="previewImage(this, 'preview1_{{ $item->ID_PENITIPAN }}')">
                                        <small class="text-muted">Format: JPG, PNG, JPEG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah.</small>
                                    </div>

                                    {{-- Upload Foto Produk 2 --}}
                                    <div class="form-group">
                                        <label class="font-weight-bold">Foto Produk 2:</label>
                                        @if($item->barang && $item->barang->foto_produk2)
                                            <div class="mb-2">
                                                <img src="{{ asset('images/fotoProduk2/' . $item->barang->foto_produk2) }}" 
                                                     class="img-thumbnail" style="max-width: 100px; max-height: 100px;"
                                                     alt="Foto Produk 2" id="preview2_{{ $item->ID_PENITIPAN }}">
                                                <small class="text-muted d-block">Foto saat ini</small>
                                            </div>
                                        @endif
                                        <input type="file" name="foto_produk2" class="form-control-file" 
                                               accept="image/jpeg,image/png,image/jpg"
                                               onchange="previewImage(this, 'preview2_{{ $item->ID_PENITIPAN }}')">
                                        <small class="text-muted">Format: JPG, PNG, JPEG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah.</small>
                                    </div>

                                    {{-- Informasi Barang (Read Only) --}}
                                    @if($item->barang)
                                        <hr>
                                        <h6 class="font-weight-bold text-muted mb-2">Detail Barang Saat Ini:</h6>
                                        <div class="small text-muted">
                                            <div><strong>Nama:</strong> {{ $item->barang->NAMA_BARANG }}</div>
                                            <div><strong>Kategori:</strong> {{ $item->barang->KATEGORI ?? 'N/A' }}</div>
                                            <div><strong>Harga:</strong> Rp {{ number_format($item->barang->HARGA, 0, ',', '.') }}</div>
                                            <div><strong>Status:</strong> {{ $item->barang->STATUS }} </div>                                                
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$(this).closest('.modal').modal('hide')">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn{{ $item->ID_PENITIPAN }}">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Modal Detail Penitipan --}}
@foreach($penitipan as $item)
<div class="modal fade" id="detailModal{{ $item->ID_PENITIPAN }}" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel{{ $item->ID_PENITIPAN }}" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailModalLabel{{ $item->ID_PENITIPAN }}">
                    <i class="fas fa-info-circle"></i> Detail Penitipan PNT-{{ str_pad($item->ID_PENITIPAN, 4, '0', STR_PAD_LEFT) }}
                </h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Kolom Kiri --}}
                    <div class="col-md-6">
                        {{-- Informasi Penitipan --}}
                         <div class="card border-left-primary mb-3">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i class="fas fa-handshake"></i> Informasi Penitipan
                                </h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%" class="font-weight-bold">Kode Penitipan:</td>
                                        <td>PNT-{{ str_pad($item->ID_PENITIPAN, 4, '0', STR_PAD_LEFT) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Status:</td>
                                        <td>
                                            {{ $item->STATUS_PENITIPAN }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Tanggal Mulai:</td>
                                        <td>{{ $item->TANGGAL_MULAI ? \Carbon\Carbon::parse($item->TANGGAL_MULAI)->format('d/m/Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Tanggal Berakhir:</td>
                                        <td>{{ $item->TANGGAL_BERAKHIR ? \Carbon\Carbon::parse($item->TANGGAL_BERAKHIR)->format('d/m/Y') : 'N/A' }}</td>
                                    </tr>
                                    @if($item->STATUS_PENITIPAN == 'Aktif' && $item->TANGGAL_BERAKHIR)
                                        <tr>
                                            <td class="font-weight-bold">Sisa Waktu:</td>
                                            <td>
                                                @if($item->STATUS_PENITIPAN == 'Aktif' && $item->TANGGAL_BERAKHIR)
                                                    @php
                                                        $tanggalBerakhir = \Carbon\Carbon::parse($item->TANGGAL_BERAKHIR)->endOfDay();
                                                        $sekarang = \Carbon\Carbon::now();
                                                        $sisaHari = $sekarang->diffInDays($tanggalBerakhir, false);
                                                    @endphp
                                                    @if($sisaHari < 0)
                                                        <span class="text-danger font-weight-bold">
                                                            <i class="fas fa-exclamation-circle"></i> {{ substr((string) abs($sisaHari), 0, 2) }} hari terlambat
                                                        </span>
                                                    @elseif($sisaHari == 0)
                                                        <span class="text-warning font-weight-bold">
                                                            <i class="fas fa-clock"></i> Berakhir hari ini
                                                        </span>
                                                    @elseif($sisaHari <= 7)
                                                        <span class="text-warning font-weight-bold">
                                                            <i class="fas fa-clock"></i> {{ substr((string) $sisaHari, 0, 2) }} hari lagi
                                                        </span>
                                                    @else
                                                        <span class="text-success">
                                                            {{ substr((string) $sisaHari, 0, 2) }} hari lagi
                                                        </span>
                                                    @endif

                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        {{-- Informasi Tracking --}}
                        <div class="card border-left-info mb-3">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-info mb-3">
                                    <i class="fas fa-user-check"></i> Informasi Tracking
                                </h6>
                                <table class="table table-sm table-borderless">
                                    {{-- Informasi Create --}}
                                    <tr>
                                        <td width="40%" class="font-weight-bold text-success">
                                            <i class="fas fa-plus-circle"></i> Dibuat Oleh:
                                        </td>
                                        <td>
                                            @if($item->CREATED_BY_NAME)
                                                {{ $item->CREATED_BY_NAME }}
                                            @elseif($item->createdByPegawai)
                                                {{ $item->createdByPegawai->NAMA_PEGAWAI }}
                                            @else
                                                {{ $item->pegawai->NAMA_PEGAWAI ?? 'N/A' }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold text-success">
                                            <i class="fas fa-calendar-plus"></i> Tanggal Dibuat:
                                        </td>
                                        <td>
                                            @if($item->CREATED_AT_FORMATTED)
                                                {{ \Carbon\Carbon::parse($item->CREATED_AT_FORMATTED)->format('d/m/Y H:i:s') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    
                                    {{-- Separator dan informasi update jika ada --}}
                                    @if($item->UPDATED_BY_NAME)
                                        <tr>
                                            <td colspan="2"><hr class="my-2"></td>
                                        </tr>
                                        
                                        <tr>
                                            <td class="font-weight-bold text-warning">
                                                <i class="fas fa-edit"></i> Terakhir Diupdate:
                                            </td>
                                            <td>{{ $item->UPDATED_BY_NAME }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-warning">
                                                <i class="fas fa-calendar-edit"></i> Tanggal Update:
                                            </td>
                                            <td>
                                                {{-- ✅ FIX: Cek berbagai kolom tanggal update --}}
                                                @if($item->UPDATED_AT_FORMATTED)
                                                    {{ \Carbon\Carbon::parse($item->UPDATED_AT_FORMATTED)->format('d/m/Y H:i:s') }}
                                                @elseif($item->TANGGAL_UPDATE)
                                                    {{ \Carbon\Carbon::parse($item->TANGGAL_UPDATE)->format('d/m/Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    
                                    {{-- Status Aktivitas --}}
                                    <tr>
                                        <td colspan="2"><hr class="my-2"></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold text-primary">
                                            <i class="fas fa-info-circle"></i> Status Aktivitas:
                                        </td>
                                        <td>
                                            @if($item->UPDATED_BY_NAME)
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-edit"></i> Pernah Diupdate
                                                </span>
                                            @else
                                                <span class="badge badge-success">
                                                    <i class="fas fa-plus"></i> Belum Pernah Diupdate
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Informasi Penitip --}}
                        <div class="card border-left-success">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-success mb-3">
                                    <i class="fas fa-user"></i> Informasi Penitip
                                </h6>
                                @if($item->penitip)
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="40%" class="font-weight-bold">Nama:</td>
                                            <td>{{ $item->penitip->NAMA_PENITIP }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">No. Telepon:</td>
                                            <td>{{ $item->penitip->NO_TELEPON_PENITIP ?? 'Tidak tersedia' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Email:</td>
                                            <td>{{ $item->penitip->EMAIL_PENITIP ?? 'Tidak tersedia' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Alamat:</td>
                                            <td>{{ $item->penitip->ALAMAT_PENITIP ?? 'Tidak tersedia' }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <div class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Data penitip tidak ditemukan
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan --}}
                    <div class="col-md-6">
                        {{-- Informasi Barang dan Foto --}}
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-warning mb-3">
                                    <i class="fas fa-box"></i> Informasi Barang
                                </h6>
                                @if($item->barang)
                                    {{-- Foto Barang --}}
                                    <div class="mb-3">
                                        <div class="row">
                                            @if($item->barang->foto_produk)
                                                <div class="col-6">
                                                    <img src="{{ asset('images/fotoProduk/' . $item->barang->foto_produk) }}" 
                                                         class="img-fluid rounded shadow-sm" 
                                                         alt="Foto Produk 1"
                                                         style="max-height: 150px; width: 100%; object-fit: cover;"
                                                         data-toggle="modal"
                                                         data-target="#imageModal{{ $item->ID_PENITIPAN }}_1"
                                                         style="cursor: pointer;">
                                                    <small class="text-muted d-block text-center mt-1">Foto 1</small>
                                                </div>
                                            @endif
                                            @if($item->barang->foto_produk2)
                                                <div class="col-6">
                                                    <img src="{{ asset('images/fotoProduk2/' . $item->barang->foto_produk2) }}" 
                                                         class="img-fluid rounded shadow-sm" 
                                                         alt="Foto Produk 2"
                                                         style="max-height: 150px; width: 100%; object-fit: cover;"
                                                         data-toggle="modal"
                                                         data-target="#imageModal{{ $item->ID_PENITIPAN }}_2"
                                                         style="cursor: pointer;">
                                                    <small class="text-muted d-block text-center mt-1">Foto 2</small>
                                                </div>
                                            @endif
                                            @if(!$item->barang->foto_produk && !$item->barang->foto_produk2)
                                                <div class="col-12">
                                                    <div class="bg-light rounded p-3 text-center">
                                                        <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                                        <p class="text-muted mb-0">Foto tidak tersedia</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Detail Barang --}}
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="40%" class="font-weight-bold">Nama Barang:</td>
                                            <td>{{ $item->barang->NAMA_BARANG }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Kategori:</td>
                                            <td>{{ $item->barang->KATEGORI ?? 'Tidak tersedia' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Harga:</td>
                                            <td>Rp {{ number_format($item->barang->HARGA, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <tr>
                                            <td class="font-weight-bold">Status Barang:</td>
                                            <td>
                                                {{ $item->barang->STATUS }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Stok:</td>
                                            <td>{{ $item->barang->stok ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Garansi:</td>
                                            <td>{{ $item->barang->GARANSI ?? 'N/A' }}</td>
                                        </tr>
                                        @if($item->barang->DESKRIPSI)
                                            <tr>
                                                <td class="font-weight-bold" style="vertical-align: top;">Deskripsi:</td>
                                                <td>{{ $item->barang->DESKRIPSI }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                @else
                                    <div class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Data barang tidak ditemukan
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Riwayat Update --}}
                @if($item->TANGGAL_UPDATE)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-light border">
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> 
                                    Terakhir diperbarui: {{ \Carbon\Carbon::parse($item->TANGGAL_UPDATE)->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>  
                @endif
            </div>
            <div class="modal-footer">
                <a href="{{ route('gudang.penitipan.preview', $item->ID_PENITIPAN) }}" 
                   class="btn btn-info btn-sm" target="_blank">
                    <i class="fas fa-eye"></i> Preview Nota
                </a>
                <a href="{{ route('gudang.penitipan.pdf', $item->ID_PENITIPAN) }}" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-download"></i> Download PDF
                </a>
                <a href="{{ route('gudang.penitipan.print', $item->ID_PENITIPAN) }}" 
                   class="btn btn-success btn-sm" target="_blank">
                    <i class="fas fa-print"></i> Print Nota
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk melihat foto lebih besar --}}
@if($item->barang && $item->barang->foto_produk)
<div class="modal fade" id="imageModal{{ $item->ID_PENITIPAN }}_1" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Produk 1</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ asset('images/fotoProduk/' . $item->barang->foto_produk) }}" 
                     class="img-fluid" alt="Foto Produk 1">
            </div>
        </div>
    </div>
</div>
@endif

@if($item->barang && $item->barang->foto_produk2)
<div class="modal fade" id="imageModal{{ $item->ID_PENITIPAN }}_2" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Produk 2</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ asset('images/fotoProduk2/' . $item->barang->foto_produk2) }}" 
                     class="img-fluid" alt="Foto Produk 2">
            </div>
        </div>
    </div>
</div>
@endif

@endforeach

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Function untuk preview gambar
    window.previewImage = function(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Function untuk auto-calculate tanggal berakhir (+30 hari dari tanggal mulai)
    function autoCalculateEndDate(startDateId, endDateId) {
        const startDate = new Date($(startDateId).val());
        if (!isNaN(startDate)) {
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 30);
            
            // Format tanggal ke YYYY-MM-DD untuk input date
            const formattedEndDate = endDate.toISOString().split('T')[0];
            $(endDateId).val(formattedEndDate);
        }
    }

    // Setup event listeners untuk semua modal update penitipan
    @if(isset($penitipan) && count($penitipan) > 0)
        @foreach($penitipan as $item)
            // Auto-calculate tanggal berakhir saat tanggal mulai berubah
            $('#TANGGAL_MULAI{{ $item->ID_PENITIPAN }}').on('change', function() {
                autoCalculateEndDate('#TANGGAL_MULAI{{ $item->ID_PENITIPAN }}', '#TANGGAL_BERAKHIR{{ $item->ID_PENITIPAN }}');
            });

            // Set tanggal berakhir otomatis saat modal dibuka jika tanggal mulai adalah hari ini
            $('#updateModal{{ $item->ID_PENITIPAN }}').on('shown.bs.modal', function() {
                const today = new Date().toISOString().split('T')[0];
                const currentStartDate = $('#TANGGAL_MULAI{{ $item->ID_PENITIPAN }}').val();
                
                // Jika tanggal mulai adalah hari ini, set tanggal berakhir otomatis
                if (currentStartDate === today) {
                    autoCalculateEndDate('#TANGGAL_MULAI{{ $item->ID_PENITIPAN }}', '#TANGGAL_BERAKHIR{{ $item->ID_PENITIPAN }}');
                }
            });

            // Handle form submission dengan AJAX
            $('#updateForm{{ $item->ID_PENITIPAN }}').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = $('#submitBtn{{ $item->ID_PENITIPAN }}');
                const errorAlert = $('#errorAlert{{ $item->ID_PENITIPAN }}');
                const errorList = $('#errorList{{ $item->ID_PENITIPAN }}');
                
                // Disable submit button dan ubah text
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                
                // Hide error alert
                errorAlert.hide();
                errorList.empty();
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Tampilkan pesan sukses
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                // Tutup modal dan reload halaman
                                $('#updateModal{{ $item->ID_PENITIPAN }}').modal('hide');
                                location.reload();
                            });
                        } else {
                            throw new Error(response.message || 'Terjadi kesalahan');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                        
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            errorList.empty();
                            
                            $.each(errors, function(field, messages) {
                                $.each(messages, function(index, message) {
                                    errorList.append('<li>' + message + '</li>');
                                });
                            });
                            
                            errorAlert.show();
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                            errorList.html('<li>' + errorMessage + '</li>');
                            errorAlert.show();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage
                            });
                        }
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
                    }
                });
            });
        @endforeach
    @endif

    // Handle error modal state
    @if(session('modal_error'))
        $('#updateModal{{ session('modal_error') }}').modal('show');
    @endif

    // Validasi tanggal berakhir tidak boleh kurang dari tanggal mulai
    $('[id^="TANGGAL_BERAKHIR"]').on('change', function() {
        const endDateId = $(this).attr('id');
        const penitipanId = endDateId.replace('TANGGAL_BERAKHIR', '');
        const startDate = new Date($('#TANGGAL_MULAI' + penitipanId).val());
        const endDate = new Date($(this).val());
        
        if (endDate <= startDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Tanggal berakhir harus lebih besar dari tanggal mulai.',
                confirmButtonText: 'OK'
            });
            
            // Reset ke tanggal mulai + 30 hari
            const newEndDate = new Date(startDate);
            newEndDate.setDate(startDate.getDate() + 30);
            $(this).val(newEndDate.toISOString().split('T')[0]);
        }
    });

    // Set minimum date untuk tanggal mulai (hari ini)
    const today = new Date().toISOString().split('T')[0];
    $('[id^="TANGGAL_MULAI"]').attr('min', today);
    
    // Set minimum date untuk tanggal berakhir berdasarkan tanggal mulai
    $('[id^="TANGGAL_MULAI"]').on('change', function() {
        const startDateId = $(this).attr('id');
        const penitipanId = startDateId.replace('TANGGAL_MULAI', '');
        const selectedDate = $(this).val();
        
        // Set minimum date untuk tanggal berakhir
        const minEndDate = new Date(selectedDate);
        minEndDate.setDate(minEndDate.getDate() + 1);
        $('#TANGGAL_BERAKHIR' + penitipanId).attr('min', minEndDate.toISOString().split('T')[0]);
    });

    // FIX MODAL BUTTON ISSUES
    // Handle modal show
    $(document).on('click', '[data-toggle="modal"]', function(e) {
        e.preventDefault();
        const target = $(this).data('target');
        if (target) {
            $(target).modal('show');
        }
    });

    // Handle modal close buttons
    $(document).on('click', '[data-dismiss="modal"]', function(e) {
        e.preventDefault();
        const $modal = $(this).closest('.modal');
        if ($modal.length > 0) {
            $modal.modal('hide');
        }
    });

    // Handle backdrop click
    $('.modal').on('click', function(e) {
        if (e.target === this) {
            $(this).modal('hide');
        }
    });

    // Handle ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            $('.modal.show').modal('hide');
        }
    });
});

// Function untuk refresh data tanpa reload halaman (optional)
function refreshPenitipanData() {
    // Implementasi refresh data via AJAX jika diperlukan
    location.reload(); // Sementara pakai reload
}
</script>

{{-- Sweet Alert untuk notifikasi --}}
@if(session('success'))
<script>
$(document).ready(function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{!! session('success') !!}',
        showConfirmButton: false,
        timer: 3000
    });
});
</script>
@endif

@if(session('error'))
<script>
$(document).ready(function() {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{!! session('error') !!}',
        confirmButtonText: 'OK'
    });
});
</script>
@endif

{{-- Pastikan SweetAlert2 sudah di-load --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Buka modal jika ada error
    @if(session('modal_error'))
        $('#updateModal{{ session("modal_error") }}').modal('show');
    @endif

    // Validasi tanggal
    $('[id^="TANGGAL_MULAI"], [id^="TANGGAL_BERAKHIR"]').on('change', function() {
        var modalId = $(this).attr('id').replace('TANGGAL_MULAI', '').replace('TANGGAL_BERAKHIR', '');
        var tanggalMulai = $('#TANGGAL_MULAI' + modalId).val();
        var tanggalBerakhir = $('#TANGGAL_BERAKHIR' + modalId).val();
        
        if (tanggalMulai && tanggalBerakhir) {
            if (new Date(tanggalBerakhir) <= new Date(tanggalMulai)) {
                alert('Tanggal berakhir harus setelah tanggal mulai');
                $(this).val('');
            }
        }
    });

    // Auto-set tanggal berakhir ketika tanggal mulai berubah (H+30)
    $('[id^="TANGGAL_MULAI"]').on('change', function() {
        var modalId = $(this).attr('id').replace('TANGGAL_MULAI', '');
        var tanggalMulai = new Date($(this).val());
        
        if (!isNaN(tanggalMulai.getTime())) {
            // Tambah 30 hari
            tanggalMulai.setDate(tanggalMulai.getDate() + 30);
            var tanggalBerakhir = tanggalMulai.toISOString().split('T')[0];
            $('#TANGGAL_BERAKHIR' + modalId).val(tanggalBerakhir);
        }
    });

    // Validasi file upload
    $('input[type="file"]').on('change', function() {
        var file = this.files[0];
        if (file) {
            // Cek ukuran file (2MB = 2097152 bytes)
            if (file.size > 2097152) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                $(this).val('');
                return;
            }
            
            // Cek tipe file
            var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Tipe file tidak didukung. Gunakan JPG, PNG, atau JPEG.');
                $(this).val('');
                return;
            }
        }
    });
});

// Handle form submission untuk setiap modal - TANPA LOADING STATE
document.addEventListener('DOMContentLoaded', function() {
    @foreach($penitipan as $item)
    const form{{ $item->ID_PENITIPAN }} = document.getElementById('updateForm{{ $item->ID_PENITIPAN }}');
    if (form{{ $item->ID_PENITIPAN }}) {
        form{{ $item->ID_PENITIPAN }}.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formId = '{{ $item->ID_PENITIPAN }}';
            const form = this;
            const submitBtn = document.getElementById('submitBtn' + formId);
            const errorAlert = document.getElementById('errorAlert' + formId);
            const errorList = document.getElementById('errorList' + formId);
            
            // Validasi form sebelum submit
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Konfirmasi sebelum menyimpan
            if (!confirm('Apakah Anda yakin ingin menyimpan perubahan data penitipan ini?')) {
                return;
            }
            
            // Hide error alert
            if (errorAlert) errorAlert.style.display = 'none';
                
            // Buat FormData untuk menangani file upload
            const formData = new FormData(form);
            
            // Submit dengan fetch API
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.ok) {
                    // Jika berhasil, show success alert dan reload
                    alert('Data penitipan berhasil diperbarui!');
                    window.location.reload();
                } else {
                    // Jika ada error dari server
                    return response.json().then(data => {
                        throw data;
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Tampilkan error alert
                let errorMessage = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
                
                if (error.errors) {
                    // Laravel validation errors
                    errorMessage = 'Terdapat kesalahan validasi:';
                    if (errorList) {
                        errorList.innerHTML = '';
                        Object.keys(error.errors).forEach(key => {
                            error.errors[key].forEach(message => {
                                const li = document.createElement('li');
                                li.textContent = message;
                                errorList.appendChild(li);
                            });
                        });
                    }
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                // Show error alert
                alert(errorMessage);
                
                // Show error alert div if exists
                if (errorAlert) {
                    if (error.errors && errorList) {
                        errorAlert.style.display = 'block';
                    }
                }
            });
        });
    }
    @endforeach
});

// Function untuk preview foto sebelum upload
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewElement = document.getElementById(previewId);
            if (previewElement) {
                previewElement.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}   
</script>
@endsection