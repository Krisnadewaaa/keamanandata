<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Request Donasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        h3 {
            text-align: left; /* Changed from center to left */
            margin-bottom: 5px;
        }
        .header-info {
            text-align: left; /* Changed from center to left */
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
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f0f0f0;
            text-align: center;
        }
        .filter-info {
            font-size: 10px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            padding: 5px;
            border-left: 3px solid #007bff;
        }
        .status-info {
            font-size: 10px;
            margin-bottom: 10px;
            background-color: #fff3cd;
            padding: 5px;
            border-left: 3px solid #ffc107;
        }
    </style>
</head>
<body>

    <h3>
        @if(isset($statusFilter) && $statusFilter === 'pending')
            Rekap request donasi (semua yang belum terpenuhi)
        @else
            Rekap request donasi (semua status)
        @endif
    </h3>

    <div class="header-info">
        <div><strong>ReUse Mart</strong></div>
        <div>Jl. Green Eco Park No. 456 Yogyakarta</div>
        <br>
        <div><strong>LAPORAN REQUEST DONASI</strong></div>
        <div>Tanggal cetak: {{ $tanggalCetak }}</div>
    </div>

    @if(isset($filterInfo))
    <div class="filter-info">
        <strong>Filter Laporan:</strong> {{ $filterInfo }}
    </div>
    @endif

    @if(isset($statusFilter))
    <div class="status-info">
        <strong>Status:</strong> 
        @if($statusFilter === 'pending')
            Hanya menampilkan request yang belum terpenuhi (pending)
        @else
            Menampilkan semua status request donasi
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID Organisasi</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Request</th>
                <th>Tanggal Request</th>
                @if(!isset($statusFilter) || $statusFilter !== 'pending')
                <th>Status</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
                @php
                    $org = $req->organisasi;
                @endphp
                <tr>
                    <td>ORG{{ $org->ID_ORGANISASI ?? '-' }}</td>
                    <td>{{ $org->NAMA_ORGANISASI ?? '-' }}</td>
                    <td>{{ $org->ALAMAT_ORGANISASI ?? '-' }}</td>
                    <td>{{ $req->ISI_REQUEST ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($req->TANGGAL_DONASI)->format('d/m/Y') }}</td>
                    @if(!isset($statusFilter) || $statusFilter !== 'pending')
                    <td>
                        @if($req->status === 'approved')
                            Disetujui
                        @elseif($req->status === 'rejected')
                            Ditolak
                        @elseif(is_null($req->status))
                            Pending
                        @else
                            {{ ucfirst($req->status) }}
                        @endif
                    </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ (!isset($statusFilter) || $statusFilter !== 'pending') ? '6' : '5' }}" style="text-align: center;">
                        @if(isset($statusFilter) && $statusFilter === 'pending')
                            Tidak ada request donasi pending untuk periode yang dipilih.
                        @else
                            Tidak ada request donasi untuk periode yang dipilih.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($requests->count() > 0)
    <div style="margin-top: 20px; font-size: 10px;">
        <strong>Total Request:</strong> {{ $requests->count() }} permintaan
        @if(isset($statusFilter) && $statusFilter === 'pending')
            (belum terpenuhi)
        @endif
    </div>
    @endif

</body>
</html>