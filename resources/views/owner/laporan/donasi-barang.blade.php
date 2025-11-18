<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Donasi Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        h3 {
            text-align: left; /* Changed from center to left */
            margin-bottom: 0;
        }
        .header-info {
            text-align: left; /* Changed from center to left */
            font-size: 12px;
            margin-bottom: 10px;
        }
        .info {
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .filter-info {
            font-size: 10px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            padding: 5px;
            border-left: 3px solid #007bff;
        }
    </style>
</head>
<body>

    <h3>LAPORAN DONASI BARANG</h3>
    <div class="header-info">
        <div><strong>ReUse Mart</strong></div>
        <div>Jl. Green Eco Park No. 456, Yogyakarta</div>
        <div class="info">
            Tanggal Cetak: {{ $tanggalCetak }}
        </div>
    </div>

    @if(isset($filterInfo))
    <div class="filter-info">
        <strong>Filter Laporan:</strong> {{ $filterInfo }}
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>ID Penitip</th>
                <th>Nama Penitip</th>
                <th>Tanggal Donasi</th>
                <th>Organisasi</th>
                <th>Nama Penerima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donasi as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if($item->barang)
                            {{ $item->barang->ID_BARANG }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->barang->NAMA_BARANG ?? '-' }}</td>
                    <td>
                        @if($item->barang && $item->barang->penitipan && $item->barang->penitipan->penitip)
                            T{{ $item->barang->penitipan->penitip->ID_PENITIP }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->barang->penitipan->penitip->NAMA_PENITIP ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->TANGGAL_DONASI)->format('d/m/Y') }}</td>
                    <td>{{ $item->organisasi->NAMA_ORGANISASI ?? '-' }}</td>
                    <td>{{ $item->organisasi->NAMA_ORGANISASI ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data donasi untuk periode yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($donasi->count() > 0)
    <div style="margin-top: 20px; font-size: 10px;">
        <strong>Total Donasi:</strong> {{ $donasi->count() }} item
    </div>
    @endif

</body>
</html>