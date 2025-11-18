@extends('layouts.gudang')

@section('title', 'Jadwalkan Pengiriman')

@section('content')
<div class="container mt-4">
    <h3>Jadwalkan Pengiriman untuk Transaksi ID: {{ $transaksi->ID_TRANSAKSI }}</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('gudang.transaksi.jadwalkan.proses', $transaksi->ID_TRANSAKSI) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="tanggal_kirim" class="form-label">Tanggal Pengiriman</label>
            <input type="date" class="form-control" id="tanggal_kirim" name="tanggal_kirim"
                   value="{{ old('tanggal_kirim') ?? now()->toDateString() }}"
                   min="{{ now()->toDateString() }}">
        </div>

        <div class="mb-3">
            <label for="kurir_id" class="form-label">Pilih Kurir</label>
            <select class="form-select" id="kurir_id" name="kurir_id" required>
                <option value="" disabled selected>-- Pilih Kurir --</option>
                @foreach ($kurirs as $kurir)
                <option value="{{ $kurir->ID_PEGAWAI }}">{{ $kurir->NAMA_PEGAWAI }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Jadwalkan</button>
        <a href="{{ route('gudang.transaksi.pending') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tanggalInput = document.getElementById('tanggal_kirim');
        const now = new Date();

        if (now.getHours() >= 16) {
            const today = now.toISOString().split('T')[0];
            if (tanggalInput.value === today) {
                alert('Pengiriman tidak bisa dijadwalkan di hari yang sama setelah jam 16.00');
                tanggalInput.value = '';
            }
            tanggalInput.min = today;
        }
    });
</script>
@endsection
