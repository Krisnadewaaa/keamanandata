@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4 text-center">Tentang ReUseMart</h1>
            
            <div class="bg-success bg-opacity-10 p-4 rounded mb-4">
                <div class="text-center mb-3">
                    <img src="{{ asset('images/ReUseMart.png') }}" alt="ReUseMart Logo" width="100">
                </div>
                <p class="lead text-center mb-0">
                    Platform jual beli barang bekas berkualitas yang mendukung gerakan reduce, reuse, recycle untuk lingkungan yang lebih baik.
                </p>
            </div>
            
            <h3 class="mb-3">Visi & Misi</h3>
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Visi</h5>
                    <p>Menjadi platform terdepan dalam menciptakan ekosistem ekonomi sirkular melalui jual beli barang bekas berkualitas, serta berkontribusi dalam pengurangan sampah dan peningkatan kesadaran lingkungan.</p>
                    
                    <h5>Misi</h5>
                    <ul>
                        <li>Memfasilitasi masyarakat untuk menjual dan membeli barang bekas berkualitas dengan mudah dan aman.</li>
                        <li>Memberikan edukasi tentang pentingnya prinsip reduce, reuse, recycle dalam kehidupan sehari-hari.</li>
                        <li>Mendorong perubahan pola pikir masyarakat terhadap barang bekas.</li>
                        <li>Berkontribusi pada pengurangan sampah dan limbah melalui pemanfaatan kembali barang bekas.</li>
                        <li>Menyalurkan barang yang tidak terjual kepada organisasi sosial yang membutuhkan.</li>
                    </ul>
                </div>
            </div>
            
            <h3 class="mb-3">Sejarah</h3>
            <div class="card mb-4">
                <div class="card-body">
                    <p>ReUseMart didirikan oleh Pak Raka Pratama, seorang pengusaha muda yang memiliki kepedulian tinggi terhadap isu lingkungan, pengelolaan limbah, dan konsep ekonomi sirkular. Berawal dari keprihatinan terhadap masalah penumpukan sampah dan barang bekas yang masih memiliki nilai guna, Pak Raka memutuskan untuk menciptakan solusi inovatif yang memadukan nilai sosial dan bisnis.</p>
                    
                    <p>Pada tahun 2022, ReUseMart resmi diluncurkan sebagai platform yang memfasilitasi masyarakat untuk menjual dan membeli barang bekas berkualitas. Berbeda dengan platform marketplace pada umumnya, ReUseMart menawarkan layanan utama berupa penjualan dengan sistem penitipan, yang memudahkan pemilik barang untuk menjual barang bekasnya tanpa harus repot mengurus proses penjualan sendiri.</p>
                    
                    <p>Sejak awal berdiri, ReUseMart telah menunjukkan komitmen kuat terhadap lingkungan dan sosial. Salah satu program unggulan adalah penyaluran barang yang tidak terjual kepada organisasi sosial yang membutuhkan, sehingga tidak ada barang yang terbuang sia-sia.</p>
                </div>
            </div>
            
            <h3 class="mb-3">Tim Kami</h3>
            <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
                <div class="col">
                    <div class="card h-100 text-center">
                        <img src="https://via.placeholder.com/150" class="card-img-top rounded-circle mx-auto mt-3" style="width: 150px; height: 150px;" alt="CEO">
                        <div class="card-body">
                            <h5 class="card-title">Raka Pratama</h5>
                            <p class="card-text text-muted">Founder & CEO</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 text-center">
                        <img src="https://via.placeholder.com/150" class="card-img-top rounded-circle mx-auto mt-3" style="width: 150px; height: 150px;" alt="COO">
                        <div class="card-body">
                            <h5 class="card-title">Rani Wijaya</h5>
                            <p class="card-text text-muted">Kepala Gudang</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 text-center">
                        <img src="https://via.placeholder.com/150" class="card-img-top rounded-circle mx-auto mt-3" style="width: 150px; height: 150px;" alt="CMO">
                        <div class="card-body">
                            <h5 class="card-title">Dina Putri</h5>
                            <p class="card-text text-muted">Customer Service</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <h3 class="mb-3">Mengapa Memilih ReUseMart?</h3>
            <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-check-circle text-success me-2"></i>Sistem Penitipan yang Mudah</h5>
                            <p class="card-text">Titipkan barang bekasmu, kami yang akan mengurus semuanya dari pemasaran hingga pengiriman. Anda tinggal menunggu barang terjual.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-check-circle text-success me-2"></i>Quality Control Ketat</h5>
                            <p class="card-text">Semua barang yang kami jual telah melalui proses quality control yang ketat untuk memastikan kualitasnya.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-check-circle text-success me-2"></i>Harga Terjangkau</h5>
                            <p class="card-text">Dapatkan barang berkualitas dengan harga terjangkau. Hemat uang dan berkontribusi pada pelestarian lingkungan.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-check-circle text-success me-2"></i>Donasi Sosial</h5>
                            <p class="card-text">Barang yang tidak terjual akan didonasikan ke organisasi sosial yang membutuhkan, sehingga tidak ada barang yang terbuang sia-sia.</p>
                        </div>
                    </div>
                </div>
            </div>
            
           {{-- Cek semua guard yang ada --}}
            @if (!Auth::guard('pembeli')->check() && 
                !Auth::guard('penitip')->check() && 
                !Auth::guard('organisasi')->check() && 
                !Auth::guard('pegawai')->check())
                <div class="text-center mt-5">
                    <p class="lead">Tertarik untuk bergabung dengan ReUseMart?</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('register.pembeli') }}" class="btn btn-outline-success">Daftar sebagai Pembeli</a>
                        <a href="{{ route('register.organisasi') }}" class="btn btn-outline-success">Daftar sebagai Organisasi</a>
                    </div>
                </div>
            @endif  
        </div>
    </div>
@endsection