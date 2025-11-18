<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Gudang</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .kop {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .alamat {
            font-size: 11px;
            margin-bottom: 15px;
        }
        .judul {
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 2px;
            text-decoration: underline;
        }
        .tanggal-cetak {
            font-size: 11px;
        }
        .note {
            font-size: 10px;
            border: 1px solid black;
            padding: 6px;
            float: right;
            width: 330px;
            margin-top: -80px;
            text-align: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }
        /* Lebar kolom agar seimbang */
        th:nth-child(1), td:nth-child(1) { width: 10%; }  /* Kode Produk */
        th:nth-child(2), td:nth-child(2) { width: 14%; }  /* Nama Produk */
        th:nth-child(3), td:nth-child(3) { width: 10%; }  /* ID Penitip */
        th:nth-child(4), td:nth-child(4) { width: 14%; }  /* Nama Penitip */
        th:nth-child(5), td:nth-child(5) { width: 12%; }  /* Tanggal Masuk */
        th:nth-child(6), td:nth-child(6) { width: 10%; }  /* Perpanjangan */
        th:nth-child(7), td:nth-child(7) { width: 10%; }  /* ID Hunter */
        th:nth-child(8), td:nth-child(8) { width: 14%; }  /* Nama Hunter */
        th:nth-child(9), td:nth-child(9) { width: 10%; }  /* Harga */
    </style>
</head>
<body>

    <div class="kop">ReUse Mart</div>
    <div class="alamat">Jl. Green Eco Park No. 456 Yogyakarta</div>
    <div class="judul">LAPORAN Stok Gudang</div>
    <div class="tanggal-cetak">Tanggal cetak: {{ $tanggalCetak }}</div>

    <div class="note">
        <strong>Catatan:</strong> Laporan ini hanya menampilkan stok yang masuk pada hari ini (sesuai tanggal cetak). Data stok sebelumnya tidak ditampilkan.
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Id Penitip</th>
                <th>Nama Penitip</th>
                <th>Tanggal Masuk</th>
                <th>Perpanjangan</th>
                <th>ID Hunter</th>
                <th>Nama Hunter</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangs as $barang)
                @php
                    $penitipan = $barang->penitipan;
                    $penitip = $penitipan->penitip ?? null;
                    $hunter = $penitipan->barangHunter ?? null;
                @endphp
                <tr>
                    <td>{{ $barang->ID_BARANG }}</td>
                    <td>{{ $barang->NAMA_BARANG }}</td>
                    <td>{{ $penitip?->ID_PENITIP ?? '-' }}</td>
                    <td>{{ $penitip?->NAMA_PENITIP ?? '-' }}</td>
                    <td>{{ $penitipan?->TANGGAL_MULAI ? \Carbon\Carbon::parse($penitipan->TANGGAL_MULAI)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $penitipan && $penitipan->calculateEndDate()?->gt($penitipan->TANGGAL_BERAKHIR) ? 'Ya' : 'Tidak' }}</td>
                    <td>{{ $hunter?->ID_BARANGHUNTER ?? '-' }}</td>
                    <td>{{ $hunter?->NAMA_HUNTER ?? '-' }}</td>
                    <td>{{ number_format($barang->HARGA, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
