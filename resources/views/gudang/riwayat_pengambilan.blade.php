@extends('layouts.gudang')

@section('title', 'Riwayat Pengambilan')

@section('content')
<div class="container mt-4">
    <div class="alert alert-info">
        Riwayat pengambilan barang oleh penitip yang telah selesai.
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Riwayat Pengambilan Barang oleh Penitip</h4>
        </div>
        <div class="card-body">
            @if($pengambilanList->isEmpty())
                <p class="text-muted mb-0">Belum ada barang yang diambil oleh penitip.</p>
            @else
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-success sticky-top">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Nama Penitip</th>
                                <th scope="col">Tanggal Pengambilan</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengambilanList as $index => $pengambilan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ optional($pengambilan->barang)->NAMA_BARANG ?? 'Tidak Diketahui' }}</td>
                                    <td>{{ optional($pengambilan->penitip)->NAMA_PENITIP ?? 'Tidak Diketahui' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pengambilan->updated_at)->format('d-m-Y H:i') }}</td>
                                    <td><span class="badge bg-secondary">Diambil</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
