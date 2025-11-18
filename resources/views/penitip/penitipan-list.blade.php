@extends('layout.penitip') {{-- Ganti sesuai layout-mu --}}

@section('content')
<div class="container mt-4">
    <h2>Daftar Barang yang Dititipkan</h2>

    <form method="GET" action="{{ route('penitipan.list') }}" class="mb-3">
        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Cari barang..." class="form-control">
    </form>

    @if($penitipanList->isEmpty())
        <div class="alert alert-warning">Tidak ada barang ditemukan.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Foto</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Berakhir</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead> 
            <tbody>
                @foreach($penitipanList as $penitipan) 
                    <tr>
                        <td>{{ $penitipan->barang->NAMA_BARANG ?? '-' }}</td>
                        <td>{{ $penitipan->barang->kategori->NAMA_KATEGORI ?? '-' }}</td>
                        <td>
                            @if($penitipan->barang->fotoBarang->isNotEmpty())
                                <img src="{{ asset('images/fotoProduk/'.$penitipan->barang->fotoBarang[0]->NAMA_FILE) }}" width="100">
                            @else
                                Tidak ada foto
                            @endif
                        </td>
                        <td>{{ $penitipan->TANGGAL_MULAI }}</td>
                        <td>{{ $penitipan->TANGGAL_BERAKHIR }}</td>
                        <td>{{ $penitipan->STATUS_PENITIPAN }}</td>
                        <td>
                            @if($penitipan->STATUS_PERPANJANGAN != 'TRUE')
                                <form method="POST" action="{{ route('penitipan.perpanjang', $penitipan->ID_PENITIPAN) }}">
                                    @csrf
                                    <button class="btn btn-primary btn-sm" onclick="return confirm('Yakin ingin perpanjang masa penitipan?')">Perpanjang</button>
                                </form>
                            @else
                                <span class="text-muted">Sudah diperpanjang</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
