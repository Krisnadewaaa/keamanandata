<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Bulanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        h3 {
            text-align: left;
            margin-bottom: 5px;
            text-decoration: underline;
        }
        .header-info {
            text-align: left;
            font-size: 12px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color:white;
        }
        .total-row {
            font-weight: bold;
        }
        .graph {
            margin-top: 30px;
            text-align: center;
        }
        .graph img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

    <div><strong>ReUse Mart</strong></div>
    <div>Jl. Green Eco Park No. 456, Yogyakarta</div>
    <h3>LAPORAN PENJUALAN BULANAN</h3>
    <div class="header-info">
        <div>
            Tahun: {{ $tahun }}
        </div>
        <div> 
            Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Jumlah Barang Terjual</th>
                <th>Jumlah Penjualan Kotor</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $grandQty = 0;
                $bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            @endphp
            @foreach($dataPerBulan as $i => $data)
                <tr>
                    <td>{{ $bulanIndo[$i] }}</td>
                    <td>{{ $data['jumlah_terjual'] }}</td>
                    <td>{{ number_format($data['total_penjualan'], 0, ',', '.') }}</td>
                </tr>
                @php
                    $grandTotal += $data['total_penjualan'];
                    $grandQty += $data['jumlah_terjual'];
                @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">Total</td>
                <td>{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

@if(isset($chartBase64))
<div class="graph">
    <img src="{{ $chartBase64 }}" alt="Grafik Penjualan Bulanan">
</div>
@endif

</body>
</html>
