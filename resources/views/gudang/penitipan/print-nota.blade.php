<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Nota - {{ $kodePenitipan }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            background: white;
        }
        
        .receipt {
            width: 100%;
            max-width: 80mm;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .store-info {
            font-size: 10px;
            line-height: 1.2;
        }
        
        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
            text-align: center;
            text-decoration: underline;
        }
        
        .info-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        .info-value {
            text-align: right;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin: 8px 0 5px 0;
            text-align: center;
        }
        
        .total-section {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            margin: 10px 0;
        }
        
        .total-line {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 2px;
        }
        
        .status-line {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin: 10px 0;
            padding: 5px;
            border: 1px solid #000;
        }
        
        .warning-box {
            text-align: center;
            font-size: 10px;
            margin: 8px 0;
            padding: 5px;
            border: 1px solid #000;
            background-color: #f0f0f0;
        }
        
        .signature-area {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
            font-size: 10px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 25px;
            padding-top: 3px;
        }
        
        .footer {
            border-top: 1px dashed #000;
            padding-top: 8px;
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
        }
        
        .footer-note {
            margin-bottom: 5px;
            font-style: italic;
        }
        
        .print-time {
            color: #666;
            font-size: 8px;
        }
        
        /* Print styles */
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 2mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
        
        /* Print button */
        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
    </style>
    <script>
        // Auto print when page loads
        window.onload = function() {
            // Add small delay to ensure page is fully loaded
            setTimeout(function() {
                window.print();
            }, 500);
        }
        
        function printReceipt() {
            window.print();
        }
    </script>
</head>
<body>
    <!-- Print Button (hidden when printing) -->
    <button class="print-button no-print" onclick="printReceipt()">🖨️ Print</button>
    
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="store-name">ReUse Mart</div>
            <div class="store-info">
                Jl. Green Eco Park No. 456 Yogyakarta
            </div>
        </div>
        
        <!-- Basic Info -->
        <div class="info-line">
            <span class="info-label">No Nota</span>
            <span class="info-value">: {{ $kodePenitipan }}</span>
        </div>
        <div class="info-line">
            <span class="info-label">Tanggal penitipan</span>
            <span class="info-value">: {{ \Carbon\Carbon::parse($penitipan->TANGGAL_PENITIPAN)->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-line">
            <span class="info-label">Masa penitipan sampai</span>
            <span class="info-value">: {{ $penitipan->TANGGAL_BERAKHIR ? \Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR)->format('d/m/Y') : 'N/A' }}</span>
        </div>
        
        <div style="margin: 10px 0;">
            <div style="margin-bottom: 5px;">
                <strong>Penitip :</strong> {{ $penitipan->penitip->NAMA_PENITIP ?? 'N/A' }}
            </div>
            <div style="font-size: 10px; line-height: 1.2;">
                {{ $penitipan->penitip->ALAMAT ?? 'Alamat tidak tersedia' }}<br>
                {{ $penitipan->penitip->KOTA ?? '' }}{{ $penitipan->penitip->KODE_POS ? ', ' . $penitipan->penitip->KODE_POS : '' }}<br>
                Delivery: {{ $penitipan->METODE_PENGANTARAN ?? 'Langsung ke toko' }}
            </div>
        </div>
        
        <div class="divider"></div>
        
        <!-- Item List -->
        <div style="margin: 12px 0;">
            <div style="margin-bottom: 8px;">
                <div style="display: flex; justify-content: space-between;">
                    <span>{{ $penitipan->barang->NAMA_BARANG ?? 'N/A' }}</span>
                    <span>{{ number_format($penitipan->BIAYA_PENITIPAN ?? 0, 0, '.', '.') }}</span>
                </div>
                <div style="font-size: 10px; color: #333; line-height: 1.2;">
                    {{ $penitipan->barang->kategori->NAMA_KATEGORI ?? 'N/A' }} - {{ $penitipan->TANGGAL_BERAKHIR ? \Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR)->format('M Y') : 'N/A' }}<br>
                    Berat barang: {{ $penitipan->BERAT_BARANG ?? 'N/A' }} kg
                </div>
            </div>
            
            @if($penitipan->barang->AKSESORIS)
            <div style="margin-bottom: 8px;">
                <div style="display: flex; justify-content: space-between;">
                    <span>{{ $penitipan->barang->AKSESORIS }}</span>
                    <span>{{ $penitipan->BIAYA_TAMBAHAN ? number_format($penitipan->BIAYA_TAMBAHAN, 0, '.', '.') : '0' }}</span>
                </div>
                <div style="font-size: 10px; color: #333; line-height: 1.2;">
                    Status: {{ $penitipan->STATUS_PENITIPAN }}<br>
                    Berat barang: {{ $penitipan->BERAT_TAMBAHAN ?? '0' }} kg
                </div>
            </div>
            @endif
        </div>
        
        <!-- QC Section -->
        <div style="margin: 20px 0 30px 0; text-align: center; font-size: 11px;">
            Diterima dan QC oleh:
        </div>
        
        <!-- Staff Signature -->
        <div style="margin-top: 40px; text-align: center; font-size: 11px;">
            {{ $penitipan->pegawai->KODE_PEGAWAI ?? 'P18' }} - {{ $penitipan->pegawai->NAMA_PEGAWAI ?? 'Staff' }}
        </div>
    </div>
</body>
</html>