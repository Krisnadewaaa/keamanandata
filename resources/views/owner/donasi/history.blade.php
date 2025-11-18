@extends('layouts.owner')

@section('title', 'Riwayat Donasi')

@section('content')
<div class="container mt-4">
    <h4>Riwayat Donasi</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Organisasi</th>
                <th>Barang</th>
                <th>Request Oleh</th>
                <th>Tanggal Donasi</th>
                <th>Isi Request</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($donationHistory as $index => $history)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $history->organisasi->NAMA_ORGANISASI ?? 'Organisasi Tidak Ditemukan' }}</td>
                    <td>{{ $history->barang->NAMA_BARANG ?? 'Barang Tidak Ditemukan' }}</td>
                    <td>{{ $history->pegawai->NAMA_PEGAWAI ?? 'Pegawai Tidak Ditemukan' }}</td>
                    <td>{{ \Carbon\Carbon::parse($history->TANGGAL_DONASI)->format('d-m-Y') }}</td>
                    <td>{{ $history->ISI_REQUEST }}</td>
                    <td>
                        @if ($history->status === 'approved')
                            <span class="badge bg-success text-white">Disetujui</span>
                        @elseif ($history->status === 'rejected')
                            <span class="badge bg-danger text-white">Ditolak</span>
                        @else
                            <span class="badge bg-secondary text-white">{{ ucfirst($history->status) }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada riwayat donasi yang disetujui.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection