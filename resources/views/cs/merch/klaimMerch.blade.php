@extends('layouts.cs')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Daftar Klaim Merchandise</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID Klaim</th>
                    <th>Nama Pembeli</th>
                    <th>Jenis Merchandise</th>
                    <th>Poin</th>
                    <th>Tanggal Ambil</th>
                    <th>Status</th>
                    <th>Aksi</th> <!-- kolom aksi -->
                </tr>
            </thead>
            <tbody>
                @foreach ($semuaKlaim as $klaim)
                <tr>
                    <td>{{ $klaim->ID_POINT }}</td>
                    <td>{{ $klaim->pembeli->NAMA_PEMBELI ?? '-' }}</td>
                    <td>{{ $klaim->merchandise->JENIS_MERCHANDISE ?? '-' }}</td>
                    <td>{{ $klaim->JUMLAH_POINT }}</td>
                    <td>
                        {{ $klaim->TANGGAL_AMBIL 
                            ? \Carbon\Carbon::parse($klaim->TANGGAL_AMBIL)->format('d-m-Y H:i') 
                            : '-' }}
                    </td>
                    <td>
                        @if ($klaim->TANGGAL_AMBIL)
                            <span class="badge badge-success text-dark">Sudah Diambil</span>
                        @else
                            <span class="badge badge-warning text-dark">Belum Diambil</span>
                        @endif
                    </td>

                    <td>
                        @if (!$klaim->TANGGAL_AMBIL)
                        <form action="{{ route('cs.klaim-merchandise.update', $klaim->ID_POINT) }}" method="POST" onsubmit="return confirm('Tandai klaim ini sudah diambil?');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm btn-primary">Ambil</button>
                        </form>
                        @else
                        <button class="btn btn-sm btn-secondary" disabled>Sudah Diambil</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
