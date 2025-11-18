@extends('layouts.app')

@section('title', 'Detail Penitipan')

@section('content')
@php
    use Carbon\Carbon;

    $now = Carbon::now();
    $endDate = Carbon::parse($consignment->TANGGAL_BERAKHIR);
    $daysLeft = $now->diffInDays($endDate, false); // positif jika masih aktif, negatif jika lewat
    $daysPastEnd = -$daysLeft; // jumlah hari lewat dari tanggal akhir, jika positif berarti sudah lewat

    // Ambil status barang dari data barang yang sudah kamu pass di view
    $barangStatus = $consignment->barang->STATUS ?? null;

    // Logika status dinamis
    if ($consignment->STATUS_PENITIPAN == 'Selesai' || $consignment->STATUS_PENITIPAN == 'Donasi') {
        $dynamicStatus = $consignment->STATUS_PENITIPAN;
    } elseif ($daysPastEnd > 30 && $barangStatus != 'Aktif') {
        // Jika sudah lewat 30 hari dan status barang bukan aktif, status Kadaluarsa
        $dynamicStatus = 'Kadaluarsa';
    } elseif ($barangStatus == 'Aktif') {
        // Jika status barang aktif, artinya sudah diperpanjang / aktif kembali
        $dynamicStatus = 'Aktif';
    } else {
        // Status asli dari penitipan (biasanya Aktif)
        $dynamicStatus = $consignment->STATUS_PENITIPAN;
    }
@endphp


<div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Profile Picture">
                <h5>{{ Auth::guard('penitip')->user()->NAMA_PENITIP }}</h5>
                <p class="text-muted">{{ Auth::guard('penitip')->user()->EMAIL_PENITIP }}</p>
                <div class="d-flex justify-content-center mb-2">
                    <div class="badge bg-success rounded-pill px-3 py-2">
                        <i class="fas fa-star me-1"></i> Rating: {{ number_format(Auth::guard('penitip')->user()->RATING_PENITIP, 1) }}
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('penitip.edit') }}" class="btn btn-outline-success">
                        <i class="fas fa-edit me-2"></i> Edit Profil
                    </a>
                    <a href="{{ route('penitip.password') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-key me-2"></i> Ubah Password
                    </a>
                </div>
            </div>
        </div>

        <div class="list-group mt-4">
            <a href="{{ route('penitip.profile') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user me-2"></i> Profil
            </a>
            <a href="{{ route('penitip.consignments') }}" class="list-group-item list-group-item-action active">
                <i class="fas fa-box me-2"></i> Riwayat Penitipan
            </a>
            {{-- <a href="{{ route('penitip.sales') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-chart-line me-2"></i> Riwayat Penjualan
            </a> --}}
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9">
        <div class="mb-4">
            <a href="{{ route('penitip.consignments') }}" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Riwayat Penitipan
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Penitipan</h5>
                <div>
                    <span class="badge
                        {{ $dynamicStatus == 'Aktif' ? 'bg-success' :
                           ($dynamicStatus == 'Selesai' ? 'bg-primary' :
                           ($dynamicStatus == 'Donasi' ? 'bg-info' :
                           ($dynamicStatus == 'Hangus' ? 'bg-danger' : 'bg-secondary'))) }}">
                        {{ $dynamicStatus }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Informasi Barang dan Penitipan (tidak berubah) -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Informasi Barang</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%">Kode Barang</td>
                                <td width="5%">:</td>
                                <td width="55%">{{ sprintf("%s%03d", substr($consignment->barang->NAMA_BARANG, 0, 1), $consignment->ID_PENITIPAN) }}</td>
                            </tr>
                            <tr>
                                <td>Nama Barang</td>
                                <td>:</td>
                                <td>{{ $consignment->barang->NAMA_BARANG }}</td>
                            </tr>
                            <tr>
                                <td>Deskripsi</td>
                                <td>:</td>
                                <td>{{ $consignment->barang->DESKRIPSI }}</td>
                            </tr>
                            <tr>
                                <td>Kategori</td>
                                <td>:</td>
                                <td>{{ $consignment->barang->KATEGORI ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Harga</td>
                                <td>:</td>
                                <td>Rp {{ number_format($consignment->barang->HARGA, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Garansi</td>
                                <td>:</td>
                                <td>{{ $consignment->barang->GARANSI == 'Ya' ? 'Ya' : 'Tidak' }}</td>
                            </tr>
                            @if($consignment->barang->GARANSI == 'Ya' && $consignment->barang->tanggal_garansi)
                                <tr>
                                    <td>Tanggal Garansi</td>
                                    <td>:</td>
                                    <td>{{ Carbon::parse($consignment->barang->tanggal_garansi)->format('d F Y') }}</td>
                                </tr>
                            @endif
                                <tr>
                                    @php
                                        use Illuminate\Support\Facades\DB;

                                        $ratings = DB::table('transaksi')
                                            ->where('ID_BARANG', $consignment->barang->ID_BARANG ?? null)
                                            ->whereIn('STATUS_PENGIRIMAN', ['Diambil', 'Terkirim'])
                                            ->whereNotNull('RATING_BARANG')
                                            ->pluck('RATING_BARANG')
                                            ->toArray();

                                        $average = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : null;
                                    @endphp

                                    <td>Rating Barang</td>
                                    <td>:</td>
                                    <td>
                                        @if($average)
                                            {{-- Bintang penuh --}}
                                            @for($i = 1; $i <= floor($average); $i++)
                                                <i class="fas fa-star text-warning"></i>
                                            @endfor

                                            {{-- Setengah bintang --}}
                                            @if(fmod($average, 1) >= 0.5)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            @endif

                                            {{-- Bintang kosong --}}
                                            @for($i = ceil($average); $i < 5; $i++)
                                                <i class="far fa-star text-warning"></i>
                                            @endfor

                                            <span class="ms-2">({{ number_format($average, 1) }}/5)</span>
                                        @else
                                            <span class="text-muted">Belum ada rating</span>
                                        @endif
                                    </td>
                                </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Informasi Penitipan</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%">Tanggal Mulai</td>
                                <td width="5%">:</td>
                                <td width="55%">{{ Carbon::parse($consignment->TANGGAL_MULAI)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Berakhir</td>
                                <td>:</td>
                                <td>{{ Carbon::parse($consignment->TANGGAL_BERAKHIR)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>:</td>
                                <td>
                                    <span class="badge
                                        {{ $dynamicStatus == 'Aktif' ? 'bg-success' :
                                           ($dynamicStatus == 'Selesai' ? 'bg-primary' :
                                           ($dynamicStatus == 'Donasi' ? 'bg-info' :
                                           ($dynamicStatus == 'Hangus' ? 'bg-danger' : 'bg-secondary'))) }}">
                                        {{ $dynamicStatus }}
                                    </span>
                                </td>
                            </tr>
                            @if($dynamicStatus == 'Aktif')
                                <tr>
                                    <td>Waktu Tersisa</td>
                                    <td>:</td>
                                    <td>
                                        @if($daysLeft > 0)
                                            <span class="text-success">{{ $daysLeft }} hari lagi</span>
                                        @elseif($daysLeft == 0)
                                            <span class="text-warning">Berakhir hari ini</span>
                                        @else
                                            <span class="text-danger">Telah berakhir {{ abs($daysLeft) }} hari yang lalu</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Foto Barang -->
                <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <img src="{{ asset('images/fotoProduk/' . ($consignment->barang->foto_produk ?? 'default.jpg')) }}" \
                                            class="img-thumbnail w-100" 
                                            alt="{{ $consignment->barang->NAMA_BARANG }}" 
                                            style="height: 150px; object-fit: cover;">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <img src="{{ asset('images/fotoProduk2/' . ($consignment->barang->foto_produk2 ?? 'default.jpg')) }}" 
                                            class="img-thumbnail w-100" 
                                            alt="{{ $consignment->barang->NAMA_BARANG }}" 
                                            style="height: 150px; object-fit: cover;">
                                    </div>
                                </div>
                            </div>

                <!-- Status Messages -->
                @if($dynamicStatus == 'Aktif')
                    @if($daysLeft > 7)
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-1">Penitipan Aktif</h5>
                                    <p class="mb-0">Barang Anda sedang dipasarkan oleh tim ReUseMart. Masa penitipan akan berakhir pada {{ $endDate->format('d F Y') }}.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($daysLeft > 0)
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-1">Penitipan Akan Segera Berakhir</h5>
                                    <p class="mb-0">Masa penitipan akan berakhir dalam {{ $daysLeft }} hari. Anda dapat memperpanjang masa penitipan atau bersiap untuk mengambil barang Anda.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($daysLeft == 0)
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-1">Penitipan Berakhir Hari Ini</h5>
                                    <p class="mb-0">Masa penitipan berakhir hari ini. Anda dapat memperpanjang masa penitipan atau mengambil barang Anda segera.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-1">Penitipan Telah Berakhir</h5>
                                    <p class="mb-0">Masa penitipan telah berakhir {{ abs($daysLeft) }} hari yang lalu. Anda harus mengambil barang Anda dalam 7 hari atau barang akan didonasikan.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @elseif($dynamicStatus == 'Hangus')
                    <div class="alert alert-danger">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-exclamation-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1">Penitipan Kadaluarsa</h5>
                                <p class="mb-0">Masa penitipan sudah lewat lebih dari 30 hari tanpa perpanjangan. Status barang menjadi kadaluarsa.</p>
                            </div>
                        </div>
                    </div>
                @elseif($dynamicStatus == 'Selesai')
                    <div class="alert alert-primary">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1">Barang Telah Terjual</h5>
                                <p class="mb-0">Barang Anda telah berhasil terjual.</p>
                            </div>
                        </div>
                    </div>
                @elseif($dynamicStatus == 'Donasi')
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-gift fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1">Barang Didonasikan</h5>
                                <p class="mb-0">Barang Anda telah didonasikan sesuai perjanjian.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tombol Ambil Barang -->
                @if($dynamicStatus == 'Aktif' || $dynamicStatus == 'Hangus')
                    <form action="{{ route('penitip.consignments.take', $consignment->ID_PENITIPAN) }}" method="POST" onsubmit="return confirm('Anda yakin ingin mengambil barang ini? Status penitipan akan berubah menjadi Kosong.')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-box-open me-2"></i> Ambil Barang
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
