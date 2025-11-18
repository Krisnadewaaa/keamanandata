<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Komisi Bulanan</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: left;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        .report-title {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 5px 0;
            text-decoration: underline;
        }

        .report-details p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 4px;
        }

        th {
            background-color: white;
            text-align: center;
        }

        td {
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .currency {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .total-row {
            font-weight: bold;
            background-color: white;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }

        .bold {
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <p class="bold">ReUse Mart</p>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    <!-- Report Title and Info -->
    <div class="report-title">LAPORAN KOMISI BULANAN</div>
    <div class="report-details">
        <p>Bulan : {{ $namaBulan }}</p>
        <p>Tahun : {{ $tahun }}</p>
        <p>Tanggal cetak: {{ $tanggalCetak }}</p>
    </div>

    @if($transaksi->count() > 0)
        @php
            $totalHargaJual = 0;
            $totalKomisiHunter = 0;
            $totalKomisiReusemart = 0;
            $totalBonusPenitip = 0;
        @endphp

        <table>
            <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Harga Jual</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Laku</th>
                    <th>Komisi Hunter</th>
                    <th>Komisi ReUse Mart</th>
                    <th>Bonus Penitip</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi as $t)
                    @php
                        $barang = $t->barang;
                        $penitipan = $barang->penitipan ?? null;
                        $komisi = $t->komisi;

                        if (!$penitipan || !$komisi) continue;

                        $hargaJual = $t->TOTAL_TRANSAKSI - ($t->BIAYA_PENGIRIMAN ?? 0);
                        $tanggalMasuk = \Carbon\Carbon::parse($penitipan->TANGGAL_MULAI);
                        $tanggalLaku = \Carbon\Carbon::parse($t->TANGGAL_TRANSAKSI);

                        $komisiHunter = $komisi->KOMISI_PEGAWAI ?? 0;
                        $komisiReusemart = $komisi->KOMISI_REUSEMART ?? 0;
                        $bonusPenitip = $komisi->KOMISI_PENITIP ?? 0;

                        $totalHargaJual += $hargaJual;
                        $totalKomisiHunter += $komisiHunter;
                        $totalKomisiReusemart += $komisiReusemart;
                        $totalBonusPenitip += $bonusPenitip;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $barang->ID_BARANG ?? '-' }}</td>
                        <td>{{ $barang->NAMA_BARANG ?? '-' }}</td>
                        <td class="currency">{{ number_format($hargaJual, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $tanggalMasuk->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $tanggalLaku->format('d/m/Y') }}</td>
                        <td class="currency">{{ number_format($komisiHunter, 0, ',', '.') }}</td>
                        <td class="currency">{{ number_format($komisiReusemart, 0, ',', '.') }}</td>
                        <td class="currency">{{ number_format($bonusPenitip, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                @for($i = $transaksi->count(); $i < 3; $i++)
                    <tr>
                        <td class="text-center">...</td>
                        <td>...</td>
                        <td class="currency">...</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor

                <tr class="total-row">
                    <td colspan="2" class="text-right bold">Total</td>
                    <td class="currency bold">{{ number_format($totalHargaJual, 0, ',', '.') }}</td>
                    <td colspan="2"> </td>
                    <!-- <td></td> -->
                    <td class="currency bold">{{ number_format($totalKomisiHunter, 0, ',', '.') }}</td>
                    <td class="currency bold">{{ number_format($totalKomisiReusemart, 0, ',', '.') }}</td>
                    <td class="currency bold">{{ number_format($totalBonusPenitip, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data transaksi komisi untuk bulan {{ $namaBulan }} {{ $tahun }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis pada {{ $tanggalCetak }}</p>
    </div>
</body>
</html>
