<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Masa Titip Habis</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 6px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }

        .title { font-size: 13px; font-weight: bold; margin-bottom: 4px; }
        .subtitle { margin-bottom: 10px; }
        .header { font-size: 12px; margin-bottom: 5px; }

        .left-header {
            text-align: left;
            line-height: 1.4;
            margin-bottom: 20px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>

    <div class="left-header">
        <div><strong>ReUse Mart</strong></div>
        <div>Jl. Green Eco Park No. 456 Yogyakarta</div>

        <br>

        <div class="report-title">LAPORAN Barang yang Masa Penitipannya Sudah Habis</div>
        <div>Tanggal cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Id Penitip</th>
                <th>Nama Penitip</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Akhir</th>
                <th>Batas Ambil</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penitipan as $p)
                @php
                    $barang = $p->barang;
                    $penitip = $p->penitip;
                    $tanggalMulai = \Carbon\Carbon::parse($p->TANGGAL_MULAI)->format('d/m/Y');
                    $tanggalAkhir = \Carbon\Carbon::parse($p->TANGGAL_BERAKHIR)->format('d/m/Y');
                    $batasAmbil = \Carbon\Carbon::parse($p->TANGGAL_BERAKHIR)->addDays(7)->format('d/m/Y');
                @endphp
                <tr>
                    <td>{{ $barang->ID_BARANG ?? '-' }}</td>
                    <td>{{ $barang->NAMA_BARANG ?? '-' }}</td>
                    <td>{{ $penitip->ID_PENITIP ?? '-' }}</td>
                    <td>{{ $penitip->NAMA_PENITIP ?? '-' }}</td>
                    <td>{{ $tanggalMulai }}</td>
                    <td>{{ $tanggalAkhir }}</td>
                    <td>{{ $batasAmbil }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
