@extends('layouts.gudang')

@section('title', 'Transaksi Hangus')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Daftar Transaksi Hangus</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <table class="table table-bordered table-hover">
        <thead class="table-danger">
            <tr>
                <th>#</th>
                <th>Nama Pembeli</th>
                <th>Nama Barang</th>
                <th>Tanggal Transaksi</th>
                <th>Status Transaksi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksiHangus as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($item->pembeli)->NAMA_PEMBELI ?? '-' }}</td>
                    <td>{{ optional($item->barang)->NAMA_BARANG ?? '-' }}</td>
                    <td>{{ $item->TANGGAL_TRANSAKSI ? \Carbon\Carbon::parse($item->TANGGAL_TRANSAKSI)->format('d-m-Y') : '-' }}</td>
                    <td>
                        <span class="badge 
                            {{ $item->STATUS_TRANSAKSI === 'Hangus' ? 'bg-danger' : 
                               ($item->STATUS_TRANSAKSI === 'Diproses' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ $item->STATUS_TRANSAKSI ?? '-' }}
                        </span>
                    </td>
                    <td>
                        @if ($item->STATUS_TRANSAKSI !== 'Hangus')
                            <form action="{{ route('gudang.konfirmasi-hangus', $item->ID_TRANSAKSI) }}" method="POST" onsubmit="return confirm('Yakin ingin mengubah status transaksi menjadi Hangus dan barang menjadi Donasi?')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Tandai Hangus
                                </button>
                            </form>
                        @else
                            <span class="text-muted">Sudah Hangus</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Tidak ada transaksi hangus saat ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
