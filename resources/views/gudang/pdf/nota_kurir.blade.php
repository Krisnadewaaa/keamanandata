<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Penjualan - ReUseMart</title>
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
        .section { margin-bottom: 8px; }
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
            <td>: {{ date('y.m.', strtotime($transaksi->TANGGAL_TRANSAKSI)) . $transaksi->ID_TRANSAKSI }}</td>
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
            <td>Tanggal kirim</td>
            <td>: {{ \Carbon\Carbon::parse($transaksi->TANGGAL_KIRIM)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <hr>

    <div class="bold">Pembeli:</div>
    <div>{{ $transaksi->pembeli->EMAIL_PEMBELI ?? '-' }} / {{ $transaksi->pembeli->NAMA_PEMBELI ?? '-' }}</div>
    <div>{{ $transaksi->pembeli->alamat->ALAMAT_LENGKAP ?? '-' }}</div>

    <div>
        Delivery:
        @if($transaksi->pegawai->ID_ROLE == 5)
            Kurir ReUseMart ({{ $transaksi->pegawai->ID_ROLE == 5 }})
        @else
            Kurir tidak tersedia
        @endif
    </div>

    <hr>

    <table>
        @foreach($transaksi->detailBarang ?? [] as $detail)
            <tr>
                <td>{{ $detail->barang->NAMA_BARANG ?? '-' }}</td>
                <td class="right">Rp {{ number_format($detail->HARGA ?? 0, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <hr>

    <table>
        <tr>
            <td>Harga Barang</td>
            <td class="right">Rp {{ number_format($transaksi->barang->HARGA, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Ongkos Kirim</td>
            <td class="right">Rp {{ number_format($transaksi->BIAYA_PENGIRIMAN, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total</td>
            <td class="right">Rp {{ number_format($transaksi->barang->HARGA + $transaksi->BIAYA_PENGIRIMAN, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Potongan {{ $transaksi->POIN_DITUKAR }} poin</td>
            <td class="right">- Rp {{ number_format($transaksi->POIN_DITUKAR * 10000, 0, ',', '.') }}</td>
        </tr>
        <tr class="bold">
            <td>Total Transaksi</td>
            <td class="right">Rp {{ number_format($transaksi->TOTAL_TRANSAKSI, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr>

    <div>Poin dari pesanan ini: {{ $transaksi->POIN_DITUKAR }}</div>
    <div>Total poin customer: {{ $transaksi->pembeli->POINT_PEMBELI }}</div>

    <hr>

    <div>QC oleh: {{ $transaksi->pegawai->NAMA_PEGAWAI ?? '' }} ({{ $transaksi->pegawai->ID_PEGAWAI ?? '' }})</div>

    <div class="footer">
        <div class="bold">Diterima oleh:</div>
        <div style="margin-top: 30px;">(...........................................)</div>
        <div>Tanggal: .....................................</div>
    </div>

</body>
</html>
