@extends('layouts.owner')

@section('title', 'Filter Laporan Request Donasi')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filter Laporan Request Donasi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Informasi Filter
                        </h6>
                        <p class="mb-0">
                            Gunakan form ini untuk menghasilkan laporan PDF request donasi sesuai kriteria yang Anda inginkan.
                        </p>
                    </div>

                    <form action="{{ route('owner.laporan.request-donasi') }}" method="GET" target="_blank">
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
                                    <label for="bulan" class="form-label">Bulan (Opsional)</label>
                                    <select class="form-select" id="bulan" name="bulan">
                                        <option value="">Semua Bulan</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status Request</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="pending">Belum Terpenuhi (Pending)</option>
                                        <option value="all">Semua Status</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="useCustomRange" onchange="toggleCustomRange()">
                                        <label class="form-check-label" for="useCustomRange">
                                            Gunakan rentang tanggal khusus
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="customRangeFields" style="display: none;">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <div>
                                <a href="{{ route('owner.laporan.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                                </a>
                                <a href="{{ route('owner.donasi.requests') }}" class="btn btn-primary">
                                    <i class="fas fa-list me-1"></i>Kelola Request Donasi
                                </a>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-file-pdf me-2"></i>Generate PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Informasi tambahan --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>Perbedaan Fungsi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Kelola Request Donasi</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-check text-success me-2"></i>Melihat daftar request pending</li>
                                <li><i class="fas fa-check text-success me-2"></i>Menyetujui atau menolak request</li>
                                <li><i class="fas fa-check text-success me-2"></i>Memilih barang untuk donasi</li>
                                <li><i class="fas fa-check text-success me-2"></i>Update informasi donasi</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">Laporan PDF Request</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-file-pdf text-danger me-2"></i>Generate laporan PDF</li>
                                <li><i class="fas fa-file-pdf text-danger me-2"></i>Filter berdasarkan periode</li>
                                <li><i class="fas fa-file-pdf text-danger me-2"></i>Filter berdasarkan status</li>
                                <li><i class="fas fa-file-pdf text-danger me-2"></i>Untuk dokumentasi/arsip</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCustomRange() {
    const checkbox = document.getElementById('useCustomRange');
    const customFields = document.getElementById('customRangeFields');
    const bulanSelect = document.getElementById('bulan');
    
    if (checkbox.checked) {
        customFields.style.display = 'block';
        bulanSelect.disabled = true;
        bulanSelect.value = '';
    } else {
        customFields.style.display = 'none';
        bulanSelect.disabled = false;
        document.getElementById('tanggal_mulai').value = '';
        document.getElementById('tanggal_akhir').value = '';
    }
}
</script>
@endsection