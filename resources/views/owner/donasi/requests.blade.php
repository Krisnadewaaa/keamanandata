@extends('layouts.owner')

@section('title', 'Daftar Request Donasi')

@section('content')
<div class="container mt-4">
    <h4>Daftar Request Donasi</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div id="donationCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach ($donationRequests->chunk(10) as $key => $requestsChunk)
                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Organisasi</th>
                                <th>Request Oleh</th>
                                <th>Tanggal Donasi</th>
                                <th>Isi Request</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requestsChunk as $index => $request)
                                <tr>
                                    <td>{{ ($key * 10) + $index + 1 }}</td>
                                    <td>{{ $request->organisasi->NAMA_ORGANISASI ?? 'Organisasi Tidak Ditemukan' }}</td>
                                    <td>{{ $request->pegawai->NAMA_PEGAWAI ?? 'Pegawai Tidak Ditemukan' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($request->TANGGAL_DONASI)->format('d-m-Y') }}</td>
                                    <td>{{ $request->ISI_REQUEST }}</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    </td>
                                    <td>
                                        <!-- Tombol Setujui -->
                                        <button type="button" class="btn btn-sm btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->ID_DONASI }}">
                                            Setujui
                                        </button>

                                        <!-- Modal Pilih Barang -->
                                        <div class="modal fade" id="approveModal{{ $request->ID_DONASI }}" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="{{ route('owner.donasi.approve', $request->ID_DONASI) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="approveModalLabel">Pilih Barang untuk Donasi</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="barang_id" class="form-label">Barang</label>
                                                                <select name="barang_id" class="form-select" required>
                                                                    <option value="">-- Pilih Barang --</option>
                                                                    @foreach ($barangDonasi as $barang)
                                                                        <option value="{{ $barang->ID_BARANG }}">{{ $barang->NAMA_BARANG }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <!-- Input hidden untuk organisasi_id dari request -->
                                                            <input type="hidden" name="organisasi_id" value="{{ $request->organisasi->ID_ORGANISASI }}">
                                                        </div>
                                                        
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Tombol Tolak -->
                                        <form action="{{ route('owner.donasi.reject', $request->ID_DONASI) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger w-100">Tolak</button>
                                        </form>

                                        <!-- Tombol Update -->
                                        <button type="button" class="btn btn-sm btn-primary w-100 mt-2" data-bs-toggle="modal" data-bs-target="#updateModal{{ $request->ID_DONASI }}">
                                            Update
                                        </button>

                                        <!-- Modal Update -->
                                        <div class="modal fade" id="updateModal{{ $request->ID_DONASI }}" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="{{ route('owner.donasi.update-info', $request->ID_DONASI) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="updateModalLabel">Update Informasi Donasi</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="nama_penerima" class="form-label">Nama Penerima</label>
                                                                <input type="text" name="nama_penerima" class="form-control" required value="{{ $request->organisasi->NAMA_ORGANISASI }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="tanggal_donasi" class="form-label">Tanggal Donasi</label>
                                                                <input type="date" name="tanggal_donasi" class="form-control" required value="{{ $request->TANGGAL_DONASI }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada permintaan donasi yang pending.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
