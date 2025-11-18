<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan per Kategori</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 6px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }

        .left-header {
            text-align: left;
            line-height: 1.4;
            margin-bottom: 20px;
        }

        .report-title {
            font-size: 13px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .total-row {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="left-header">
        <div><strong>ReUse Mart</strong></div>
        <div>Jl. Green Eco Park No. 456 Yogyakarta</div>

        <br>

        <div class="report-title">LAPORAN PENJUALAN PER KATEGORI BARANG</div>
        <div>Tahun : {{ $tahun }}</div>
        <div>Tanggal cetak: {{ $tanggalCetak }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah item terjual</th>
                <th>Jumlah item gagal terjual</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTerjual = 0;
                $grandGagal = 0;
            @endphp

            @foreach($kategori as $kat)
            @php
                $jumlahTerjual = 0;
                $jumlahGagal = 0;
                $barangs = $kat->barangs ?? [];

                foreach ($barangs as $barang) {
                    $transaksis = $barang->transaksi ?? collect();
                    $penitipan = $barang->penitipan;

                    // Barang dianggap berhasil terjual jika punya transaksi
                    if ($transaksis->isNotEmpty()) {
                        $jumlahTerjual++;
                    }
                    // Barang dianggap gagal terjual jika tidak punya transaksi dan status penitipannya tidak aktif
                    elseif ($penitipan && $penitipan->STATUS_PENITIPAN !== 'Aktif' && $penitipan->STATUS_PENITIPAN !== 'Donasi') {
                        $jumlahGagal++;
                    }
                }

                $grandTerjual += $jumlahTerjual;
                $grandGagal += $jumlahGagal;
            @endphp

            <tr>
                <td style="text-align: left;">{{ $kat->JENIS_KATEGORI ?? '-' }}</td>
                <td>{{ $jumlahTerjual > 0 ? $jumlahTerjual : '....' }}</td>
                <td>{{ $jumlahGagal > 0 ? $jumlahGagal : '....' }}</td>
            </tr>
        @endforeach

            <tr class="total-row">
                <td>Total</td>
                <td>{{ $grandTerjual > 0 ? $grandTerjual : '....' }}</td>
                <td>{{ $grandGagal > 0 ? $grandGagal : '....' }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
