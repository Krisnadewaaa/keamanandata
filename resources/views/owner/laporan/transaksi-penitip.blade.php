<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Penitip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: left; /* Changed from left to left (already correct) */
            margin-bottom: 30px;
        }
        
        .header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            text-align: left; /* Ensure left alignment */
        }
        
        .company-info {
            margin: 10px 0;
            font-size: 12px;
            text-align: left; /* Ensure left alignment */
        }
        
        .report-title {
            margin: 20px 0 10px 0;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            text-align: left; /* Ensure left alignment */
        }
        
        .penitip-info {
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        .penitip-info div {
            margin: 2px 0;
        }
        
        .table-container {
            width: 100%;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
        }
        
        .text-left {
            text-align: left;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .dots {
            text-align: center;
            font-weight: normal;
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

@foreach ($penitip as $p)
    @php
        // Pastikan bulan dan tahun dari parameter yang diterima
        $bulanFilter = $bulan ?? 6; // Default Juni
        $tahunFilter = $tahun ?? 2025; // Default 2025
        $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');
        $totalPendapatan = 0;
        $totalHargaBersih = 0;
        $totalBonus = 0;
        $transaksiList = [];
    @endphp

    {{-- Kumpulkan data transaksi --}}
    @foreach ($p->penitipan as $penitipan)
        @php
            $barang = $penitipan->barang;
            if (!$barang) {
                continue;
            }

            // Cari transaksi yang terkait dengan barang ini
            $transaksi = \App\Models\Transaksi::where('ID_BARANG', $barang->ID_BARANG)
                ->where('STATUS_TRANSAKSI', 'Selesai')
                ->whereMonth('TANGGAL_TRANSAKSI', $bulanFilter)
                ->whereYear('TANGGAL_TRANSAKSI', $tahunFilter)
                ->get();

            foreach ($transaksi as $trx) {
                $tglLaku = \Carbon\Carbon::parse($trx->TANGGAL_TRANSAKSI);
                
                // Hitung komisi berdasarkan data komisi
                $komisiData = $trx->komisi;
                $totalKomisi = $komisiData ? $komisiData->TOTAL_KOMISI : 0;
                
                // Harga bersih = Total transaksi - Total komisi
                $hargaBersih = $trx->TOTAL_TRANSAKSI - $totalKomisi;
                
                // Hitung bonus terjual cepat (jika terjual dalam 7 hari)
                $tanggalMasuk = \Carbon\Carbon::parse($penitipan->TANGGAL_MULAI);
                $selisihHari = $tanggalMasuk->diffInDays($tglLaku);
                $bonus = 0;
                
                if ($selisihHari <= 7) {
                    // Bonus 10% dari komisi ReUseMart
                    $komisiReusemart = $totalKomisi * 0.85; // 85% dari total komisi untuk ReUseMart
                    $bonus = $komisiReusemart * 0.10;
                }
                
                $pendapatan = $hargaBersih + $bonus;
                
                $totalHargaBersih += $hargaBersih;
                $totalBonus += $bonus;
                $totalPendapatan += $pendapatan;

                // Use ID_BARANG directly as kode produk
                $kodeProduk = $barang->ID_BARANG;

                $transaksiList[] = [
                    'kode' => $kodeProduk,
                    'nama' => $barang->NAMA_BARANG,
                    'tgl_masuk' => \Carbon\Carbon::parse($penitipan->TANGGAL_MULAI)->format('d/m/Y'),
                    'tgl_laku' => $tglLaku->format('d/m/Y'),
                    'harga_bersih' => $hargaBersih,
                    'bonus' => $bonus,
                    'pendapatan' => $pendapatan,
                ];
            }
        @endphp
    @endforeach

    {{-- Tampilkan laporan jika ada data --}}
    @if (count($transaksiList) > 0)
        <div class="header">
            <h2>Laporan untuk Penitip</h2>
            <div class="company-info">
                <strong>ReUse Mart</strong><br>
                Jl. Green Eco Park No. 456 Yogyakarta
            </div>
        </div>

        <div class="report-title">LAPORAN TRANSAKSI PENITIP</div>
        
        <div class="penitip-info">
            <div>ID Penitip : T{{ $p->ID_PENITIP }}</div>
            <div>Nama Penitip : {{ $p->NAMA_PENITIP }}</div>
            <div>Bulan : {{ \Carbon\Carbon::createFromFormat('n', $bulanFilter)->translatedFormat('F') }}</div>
            <div>Tahun : {{ $tahunFilter }}</div>
            <div>Tanggal cetak: {{ $tanggalCetak }}</div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">Kode<br>Produk</th>
                        <th style="width: 20%;">Nama Produk</th>
                        <th style="width: 12%;">Tanggal<br>Masuk</th>
                        <th style="width: 12%;">Tanggal Laku</th>
                        <th style="width: 18%;">Harga Jual Bersih (sudah<br>dipotong Komisi)</th>
                        <th style="width: 13%;">Bonus terjual<br>cepat</th>
                        <th style="width: 15%;">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksiList as $trx)
                    <tr>
                        <td>{{ $trx['kode'] }}</td>
                        <td class="text-left">{{ $trx['nama'] }}</td>
                        <td>{{ $trx['tgl_masuk'] }}</td>
                        <td>{{ $trx['tgl_laku'] }}</td>
                        <td class="text-right">{{ number_format($trx['harga_bersih'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($trx['bonus'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($trx['pendapatan'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    {{-- Baris titik-titik --}}
                    <tr>
                        <td class="dots">....</td>
                        <td class="dots">....</td>
                        <td class="dots">....</td>
                        <td class="dots">....</td>
                        <td class="dots">....</td>
                        <td class="dots">....</td>
                        <td class="dots">....</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" style="text-align: center;">TOTAL</td>
                        <td class="text-right">{{ number_format($totalHargaBersih, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalBonus, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endif
@endforeach

{{-- Jika tidak ada data untuk semua penitip --}}
@if($penitip->isEmpty() || $penitip->every(function($p) use ($bulan, $tahun) {
    $bulanFilter = $bulan ?? 6;
    $tahunFilter = $tahun ?? 2025;
    $hasTransaksi = false;
    
    foreach($p->penitipan as $penitipan) {
        if($penitipan->barang) {
            $transaksi = \App\Models\Transaksi::where('ID_BARANG', $penitipan->barang->ID_BARANG)
                ->where('STATUS_TRANSAKSI', 'Selesai')
                ->whereMonth('TANGGAL_TRANSAKSI', $bulanFilter)
                ->whereYear('TANGGAL_TRANSAKSI', $tahunFilter)
                ->exists();
            if($transaksi) {
                $hasTransaksi = true;
                break;
            }
        }
    }
    return !$hasTransaksi;
}))
    <div class="header">
        <h2>Laporan untuk Penitip</h2>
        <div class="company-info">
            <strong>ReUse Mart</strong><br>
            Jl. Green Eco Park No. 456 Yogyakarta
        </div>
    </div>

    <div class="report-title">LAPORAN TRANSAKSI PENITIP</div>
    
    <div class="penitip-info">
        <div>Bulan : {{ \Carbon\Carbon::createFromFormat('n', $bulan ?? 6)->translatedFormat('F') }}</div>
        <div>Tahun : {{ $tahun ?? 2025 }}</div>
        <div>Tanggal cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
    </div>

    <div style="text-align: center; margin-top: 50px; font-size: 14px;">
        <p><strong>Tidak ada transaksi untuk periode yang dipilih</strong></p>
        <p>Silakan pilih periode lain atau periksa kembali filter yang digunakan.</p>
    </div>
@endif

</body>
</html>