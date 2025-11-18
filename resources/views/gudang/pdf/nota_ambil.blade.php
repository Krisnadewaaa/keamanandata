<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Penjualan - ReUse Mart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
        }
        .bold { font-weight: bold; }
        .right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px; }
        hr { margin: 6px 0; }
        .footer { margin-top: 20px; }
    </style>
</head>
<body>

    <div class="bold">ReUse Mart</div>
    <div>Jl. Green Eco Park No. 456 Yogyakarta</div>

    <hr>

    <table>
        <tr>
            <td>No Nota</td>
            <td>: {{ date('y.m.', strtotime($transaksi->TANGGAL_TRANSAKSI)) }}{{ $transaksi->ID_TRANSAKSI }}</td>
        </tr>
        <tr>
            <td>Tanggal pesan</td>
            <td>: {{ \Carbon\Carbon::parse($transaksi->TANGGAL_TRANSAKSI)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Lunas pada</td>
            <td>: {{ \Carbon\Carbon::parse($transaksi->TANGGAL_LUNAS)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Tanggal ambil</td>
            <td>: {{ \Carbon\Carbon::parse($transaksi->TANGGAL_KIRIM)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <hr>

    <div class="bold">Pembeli :</div>
    <div>{{ $transaksi->pembeli->EMAIL_PEMBELI ?? '-' }} / {{ $transaksi->pembeli->NAMA_PEMBELI ?? '-' }}</div>
    <div>{{ $transaksi->pembeli->alamat->ALAMAT_LENGKAP ?? '-' }}</div>
    <div>Delivery: - (diambil sendiri)</div>

    <hr>

    <br>

    <table>
        <tr>
            <div>{{ $transaksi->barang->NAMA_BARANG ?? '-' }}</div>
            <td class="right">
                Rp {{ number_format($transaksi->barang->HARGA, 0, ',', '.') }}
            </td>
        </tr>
        {{-- <tr>
            <td>Total</td>
            <td class="right">
                Rp {{ number_format($transaksi->barang->HARGA, 0, ',', '.') }}
            </td>
        </tr> --}}
        <tr>
            <td>Ongkos Kirim</td>
            Rp {{ number_format($transaksi->BIAYA_PENGIRIMAN, 0, ',', '.') }}
        </tr>
        <tr>
            <td>Total</td>
            <td class="right">
                Rp {{ number_format($transaksi->barang->HARGA, 0, ',', '.') }}
            </td>
        </tr>
        @if(($transaksi->POIN_DITUKAR ?? 0) > 0)
        <tr>
            <td>Potongan {{ $transaksi->POINT_DITUKAR }} poin</td>
            <td class="right">- Rp {{ number_format($transaksi->POIN_DITUKAR * 10000 ?? 0, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="bold">
            <td>Total Transaksi</td>
            <td class="right">Rp {{ number_format($transaksi->TOTAL_TRANSAKSI , 0, ',', '.') }}</td>
        </tr>
    </table>

    <br>

    <div>Poin dari pesanan ini: {{ $transaksi->POIN_DITUKAR ?? 0 }}</div>
    <div>Total poin customer: {{ $transaksi->pembeli->POINT_PEMBELI ?? 0 }}</div>

    <br>

    <div>QC oleh: {{ $transaksi->pegawai->NAMA_PEGAWAI ?? '-' }} ({{ $transaksi->pegawai->ID_PEGAWAI ?? '-' }})</div>

    <div class="footer">
        <div class="bold">Diambil oleh:</div>
        <br><br>
        <div>(...........................................)</div>
        <div>Tanggal: ...............................</div>
    </div>

</body>
</html>
