@extends('layouts.owner')

@section('title', 'Filter Laporan Transaksi Penitip')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filter Laporan Transaksi Penitip</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('owner.laporan.transaksi-penitip') }}" method="GET" target="_blank">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tahun" class="form-label">Tahun</label>
                                    <select class="form-select" id="tahun" name="tahun" required>
                                        @for($year = 2020; $year <= date('Y') + 1; $year++)
                                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bulan" class="form-label">Bulan</label>
                                    <select class="form-select" id="bulan" name="bulan" required>
                                        <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>Januari</option>
                                        <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>Februari</option>
                                        <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>Maret</option>
                                        <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                                        <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>Mei</option>
                                        <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>Juni</option>
                                        <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>Juli</option>
                                        <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>Agustus</option>
                                        <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                                        <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>Oktober</option>
                                        <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>November</option>
                                        <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>Desember</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="penitip_id" class="form-label">Penitip (Opsional)</label>
                                    <select class="form-select" id="penitip_id" name="penitip_id">
                                        <option value="">Semua Penitip</option>
                                        @foreach($penitips as $penitip)
                                            <option value="{{ $penitip->ID_PENITIP }}">
                                                {{ $penitip->NAMA_PENITIP }} (ID: {{ $penitip->ID_PENITIP }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('owner.laporan.dashboard') }}" class="btn btn-secondary me-md-2">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-pdf me-2"></i>Generate PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection