<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Penitipan - {{ $kodePenitipan }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        
        .store-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .store-info {
            font-size: 9px;
            line-height: 1.1;
        }
        
        .receipt-title {
            font-size: 12px;
            font-weight: bold;
            margin: 8px 0;
            text-align: center;
            text-decoration: underline;
        }
        
        .info-section {
            margin-bottom: 8px;
        }
        
        .simple-row {
            margin-bottom: 3px;
            font-size: 11px;
            line-height: 1.3;
        }
        
        .address-info {
            margin: 8px 0;
            font-size: 10px;
            line-height: 1.2;
        }
        
        .item-list {
            margin: 12px 0;
        }
        
        .item-entry {
            margin-bottom: 6px;
            font-size: 11px;
        }
        
        .item-name {
            font-weight: normal;
        }
        
        .item-details {
            font-size: 10px;
            color: #333;
            margin-left: 0;
        }
        
        .price-right {
            float: right;
            font-weight: normal;
        }
        
        .qc-section {
            margin: 20px 0 30px 0;
            text-align: center;
            font-size: 11px;
        }
        
        .staff-signature {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        
        .item-header {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 4px;
            text-align: center;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 9px;
        }
        
        .item-name {
            width: 50%;
        }
        
        .item-detail {
            width: 50%;
            text-align: right;
            font-size: 8px;
        }
        
        .total-section {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            margin: 8px 0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 11px;
        }
        
        .status-section {
            margin: 8px 0;
            text-align: center;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .status-aktif {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-selesai {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .status-terlambat {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .warning-section {
            margin: 8px 0;
            padding: 4px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 3px;
        }
        
        .warning-text {
            font-size: 8px;
            text-align: center;
            color: #856404;
        }
        
        .footer {
            border-top: 1px dashed #000;
            padding-top: 8px;
            margin-top: 12px;
            text-align: center;
            font-size: 8px;
        }
        
        .footer-note {
            margin-bottom: 4px;
            font-style: italic;
        }
        
        .print-time {
            color: #666;
        }
        
        .qr-section {
            text-align: center;
            margin: 8px 0;
        }
        
        .signature-section {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
            font-size: 8px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 20px;
            padding-top: 2px;
        }
        
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 2mm;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="store-name">ReUse Mart</div>
        <div class="store-info">
            Jl. Green Eco Park No. 456 Yogyakarta
        </div>
    </div>
    
    <!-- Info Section -->
    <div class="info-section">
        <div class="simple-row">
            <span>No Nota</span>
            <span>: {{ $kodePenitipan }}</span>
        </div>
        <div class="simple-row">
            <span>Tanggal penitipan</span>
            <span>: {{ \Carbon\Carbon::parse($penitipan->TANGGAL_PENITIPAN)->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="simple-row">
            <span>Masa penitipan sampai</span>
            <span>: {{ $penitipan->TANGGAL_BERAKHIR ? \Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR)->format('d/m/Y') : 'N/A' }}</span>
        </div>
    </div>
    
    <!-- Customer Info -->
    <div class="info-section">
        <div class="simple-row">
            <span>Penitip :</span>
            <span>{{ $penitipan->penitip->NAMA_PENITIP ?? 'N/A' }}</span>
        </div>
        <div class="address-info">
            {{ $penitipan->penitip->ALAMAT ?? 'Alamat tidak tersedia' }}<br>
            {{ $penitipan->penitip->KOTA ?? '' }}{{ $penitipan->penitip->KODE_POS ? ', ' . $penitipan->penitip->KODE_POS : '' }}<br>
            Delivery: {{ $penitipan->METODE_PENGANTARAN ?? 'Langsung ke toko' }}
        </div>
    </div>
    
    
    <!-- Item List -->
    <div class="item-list">
        <div class="item-entry">
            <div class="item-name">{{ $penitipan->barang->NAMA_BARANG ?? 'N/A' }}</div>
            <div class="price-right">{{ number_format($penitipan->BIAYA_PENITIPAN ?? 0, 0, '.', '.') }}</div>
            <div style="clear: both;"></div>
            <div class="item-details">
                {{ $penitipan->barang->kategori->NAMA_KATEGORI ?? 'N/A' }} - {{ $penitipan->TANGGAL_BERAKHIR ? \Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR)->format('M Y') : 'N/A' }}<br>
                Berat barang: {{ $penitipan->BERAT_BARANG ?? 'N/A' }} kg
            </div>
        </div>
        
        @if($penitipan->barang->AKSESORIS)
        <div class="item-entry">
            <div class="item-name">{{ $penitipan->barang->AKSESORIS }}</div>
            <div class="price-right">{{ $penitipan->BIAYA_TAMBAHAN ? number_format($penitipan->BIAYA_TAMBAHAN, 0, '.', '.') : '0' }}</div>
            <div style="clear: both;"></div>
            <div class="item-details">
                Status: {{ $penitipan->STATUS_PENITIPAN }}<br>
                Berat barang: {{ $penitipan->BERAT_TAMBAHAN ?? '0' }} kg
            </div>
        </div>
        @endif
    </div>
    
    <!-- QC Section -->
    <div class="qc-section">
        Diterima dan QC oleh:
    </div>
    
    <!-- Staff Signature -->
    <div class="staff-signature">
        {{ $penitipan->pegawai->KODE_PEGAWAI ?? 'P18' }} - {{ $penitipan->pegawai->NAMA_PEGAWAI ?? 'Staff' }}
    </div>
</body>
</html>