@extends('layouts.gudang')

@section('title', 'Laporan Penitipan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Penitipan</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-danger btn-sm" onclick="exportToPDF()">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            <button type="button" class="btn btn-info btn-sm" onclick="printReport()">
                <i class="fas fa-print"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('gudang.penitipan.laporan') }}">
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
                            <a href="{{ route('gudang.penitipan.laporan') }}" class="btn btn-secondary">
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
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Penitipan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistik['total_penitipan'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistik['penitipan_aktif'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistik['penitipan_selesai'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Akan Berakhir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistik['akan_berakhir'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Expired</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistik['penitipan_expired'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Total Nilai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($statistik['total_nilai'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow mb-4" id="printableArea">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Laporan Penitipan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="laporanTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Penitip</th>
                            <th>Barang</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Berakhir</th>
                            <th>Status</th>
                            <th>Durasi (Hari)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penitipan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>PNT-{{ str_pad($item->ID_PENITIPAN, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                @if($item->penitip)
                                    <div class="font-weight-bold">{{ $item->penitip->NAMA_PENITIP }}</div>
                                @else
                                    <span class="text-danger">Data tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                                @if($item->barang)
                                    <div class="font-weight-bold">{{ $item->barang->NAMA_BARANG }}</div>
                                @else
                                    <span class="text-danger">Data tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                               {{ $item->barang->KATEGORI ?? 'Tidak tersedia' }}</td>
                            </td>
                            <td>Rp {{ number_format($item->barang->HARGA ?? 0, 0, ',', '.') }}</td>
                            <td>
                                {{ $item->TANGGAL_MULAI ? \Carbon\Carbon::parse($item->TANGGAL_MULAI)->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                {{ $item->TANGGAL_BERAKHIR ? \Carbon\Carbon::parse($item->TANGGAL_BERAKHIR)->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                {{ $item->STATUS_PENITIPAN }}
                            </td>
                            <td>
                                @if($item->TANGGAL_MULAI && $item->TANGGAL_BERAKHIR)
                                    {{ \Carbon\Carbon::parse($item->TANGGAL_MULAI)->diffInDays(\Carbon\Carbon::parse($item->TANGGAL_BERAKHIR)) }} hari
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data laporan penitipan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#laporanTable').DataTable({
            "pageLength": 50,
            "order": [[ 6, "desc" ]], // Sort by Tanggal Mulai descending
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            },
            "dom": 'Bfrtip',
            "buttons": [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });

    function printReport() {
        var printContents = document.getElementById('printableArea').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }

    function exportToExcel() {
        // Implementasi export Excel
        window.location.href = "{{ route('gudang.penitipan.laporan') }}?export=excel&" + window.location.search.substring(1);
    }

    function exportToPDF() {
        // Implementasi export PDF
        window.location.href = "{{ route('gudang.penitipan.laporan') }}?export=pdf&" + window.location.search.substring(1);
    }
</script>
@endsection