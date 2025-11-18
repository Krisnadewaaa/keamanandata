@extends('layouts.gudang')

@section('title', 'Tambah Penitipan Baru')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Penitipan Baru</h1>
    </div>

    <!-- Alert Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Form Card -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Penitipan</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('gudang.penitipan.store') }}">
                        @csrf
                        
                       <div class="row">
                            <!-- Pilih Penitip -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_penitip">Penitip <span class="text-danger">*</span></label>
                                    <select name="id_penitip" id="id_penitip" class="form-control" required>
                                        <option value="">-- Pilih Penitip --</option>
                                        @foreach($penitip as $p)
                                            <option value="{{ $p->ID_PENITIP }}" 
                                                    {{ old('id_penitip') == $p->ID_PENITIP ? 'selected' : '' }}
                                                    data-nama="{{ $p->NAMA_PENITIP }}"
                                                    data-telepon="{{ $p->NO_TELEPON_PENITIP }}"
                                                    data-alamat="{{ $p->ALAMAT_PENITIP }}">
                                                {{ $p->NAMA_PENITIP }} - {{ $p->NO_TELEPON_PENITIP }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Pilih Barang -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_barang">Barang <span class="text-danger">*</span></label>
                                    <select name="id_barang" id="id_barang" class="form-control" required>
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach($barang as $b)
                                            <option value="{{ $b->ID_BARANG }}" 
                                                    {{ old('id_barang') == $b->ID_BARANG ? 'selected' : '' }}
                                                    data-nama="{{ $b->NAMA_BARANG }}"
                                                    data-harga="{{ number_format($b->HARGA, 0, ',', '.') }}"
                                                    data-stok="{{ $b->stok }}"
                                                    data-kategori="{{ $b->KATEGORI ?? 'N/A' }}">
                                                {{ $b->NAMA_BARANG }} - Rp {{ number_format($b->HARGA, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="barangInfo" class="mt-2" style="display: none;">
                                        <small class="text-muted">
                                            <strong>Kategori:</strong> <span id="kategoriBarang"></span><br>
                                            <strong>Stok:</strong> <span id="stokBarang"></span> unit<br>
                                            <strong>Harga:</strong> Rp <span id="hargaBarang"></span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tanggal Mulai -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" 
                                           class="form-control" 
                                           value="{{ old('tanggal_mulai', date('Y-m-d')) }}" 
                                           required>
                                    <small class="text-muted">Tanggal dimulainya penitipan (Durasi otomatis 30 hari)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Tanggal Berakhir -->
                        <div class="form-group">
                            <div class="alert alert-info" id="previewTanggal">
                                <i class="fas fa-info-circle"></i>
                                <strong>Periode Penitipan:</strong>
                                <span id="periodeText"></span>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Penitipan
                            </button>
                            <a href="{{ route('gudang.penitipan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div> 

        <!-- Info Card -->
        <div class="col-lg-4">
            <!-- Panduan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle"></i> Panduan Penitipan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-primary">Langkah-langkah:</h6>
                        <ol class="small">
                            <li>Pilih penitip yang akan menitipkan barang</li>
                            <li>Pilih barang yang akan dititipkan</li>
                            <li>Tentukan tanggal mulai penitipan</li>
                            <li>Durasi otomatis 30 hari dari tanggal mulai</li>
                        </ol>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-warning">Catatan Penting:</h6>
                        <ul class="small">
                            <li>Barang yang sudah dalam penitipan aktif tidak bisa dipilih</li>
                            <li>Status barang akan otomatis berubah menjadi "Penitipan"</li>
                            <li>Durasi penitipan tetap 30 hari</li>
                            <li>Penitipan dapat diperpanjang setelah dibuat</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Note about adding new penitip -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-user-plus"></i> Penitip Baru?
                    </h6>
                </div>
                <div class="card-body text-center">
                    <p class="small text-muted mb-3">
                        Jika penitip belum terdaftar, silakan hubungi admin untuk menambahkan data penitip baru.
                    </p>
                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Catatan:</strong> Hanya admin yang dapat menambahkan penitip baru ke sistem.
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Statistik
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h5 class="font-weight-bold text-primary">{{ $penitip->count() }}</h5>
                                <small class="text-muted">Total Penitip</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="font-weight-bold text-success">{{ $barang->count() }}</h5>
                            <small class="text-muted">Barang Tersedia</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {

        // Show penitip info when selected
        $('#id_penitip').change(function() {
            const selectedOption = $(this).find(':selected');
            if (selectedOption.val()) {
                $('#alamatPenitip').text(selectedOption.data('alamat'));
                $('#penitipInfo').show();
            } else {
                $('#penitipInfo').hide();
            }
        });

        // Show barang info when selected
        $('#id_barang').change(function() {
            const selectedOption = $(this).find(':selected');
            if (selectedOption.val()) {
                $('#kategoriBarang').text(selectedOption.data('kategori'));
                $('#stokBarang').text(selectedOption.data('stok'));
                $('#hargaBarang').text(selectedOption.data('harga'));
                $('#barangInfo').show();
            } else {
                $('#barangInfo').hide();
            }
        });

        // Calculate and preview end date (fixed 30 days)
        function updatePreview() {
            const tanggalMulai = $('#tanggal_mulai').val();
            const durasi = 30; // Fixed 30 days
            
            if (tanggalMulai) {
                const startDate = new Date(tanggalMulai);
                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + durasi);
                
                const options = { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    timeZone: 'Asia/Jakarta'
                };
                
                const startText = startDate.toLocaleDateString('id-ID', options);
                const endText = endDate.toLocaleDateString('id-ID', options);
                
                $('#periodeText').text(`${startText} - ${endText} (${durasi} hari)`);
                $('#previewTanggal').show();
            } else {
                $('#previewTanggal').hide();
            }
        }

        // Update preview when tanggal_mulai changes
        $('#tanggal_mulai').on('change input', updatePreview);

        // Initial preview
        updatePreview();

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        $('#tanggal_mulai').attr('min', today);

        // Trigger change events for pre-selected values
        if ($('#id_penitip').val()) {
            $('#id_penitip').trigger('change');
        }
        if ($('#id_barang').val()) {
            $('#id_barang').trigger('change');
        }
    });
</script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection