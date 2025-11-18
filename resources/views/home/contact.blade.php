@extends('layouts.app')

@section('title', 'Kontak')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4 text-center">Hubungi Kami</h1>
            
            <div class="row mb-5">
                <div class="col-md-4">
                    <div class="card h-100 text-center p-3">
                        <div class="card-body">
                            <i class="fas fa-map-marker-alt fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Alamat</h5>
                            <p class="card-text">Jl. Green Eco Park No. 456<br>Yogyakarta, Indonesia</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-3">
                        <div class="card-body">
                            <i class="fas fa-phone fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Telepon</h5>
                            <p class="card-text">(0274) 123456<br>0812-3456-7890</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-3">
                        <div class="card-body">
                            <i class="fas fa-envelope fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Email</h5>
                            <p class="card-text">info@reusemart.com<br>cs@reusemart.com</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-5">
                <div class="card-body">
                    <h3 class="card-title mb-4">Kirim Pesan</h3>
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" placeholder="Masukkan nama lengkap">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Masukkan email">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subjek</label>
                            <input type="text" class="form-control" id="subject" placeholder="Masukkan subjek pesan">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Pesan</label>
                            <textarea class="form-control" id="message" rows="5" placeholder="Tulis pesan Anda di sini"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Kirim Pesan</button>
                    </form>
                </div>
            </div>
            
            <div class="card mb-5">
                <div class="card-body">
                    <h3 class="card-title mb-4">Jam Operasional</h3>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td width="200">Senin - Jumat</td>
                                    <td>08:00 - 20:00 WIB</td>
                                </tr>
                                <tr>
                                    <td>Sabtu</td>
                                    <td>09:00 - 18:00 WIB</td>
                                </tr>
                                <tr>
                                    <td>Minggu</td>
                                    <td>10:00 - 16:00 WIB</td>
                                </tr>
                                <tr>
                                    <td>Hari Libur Nasional</td>
                                    <td>Tutup</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Pengiriman barang hanya dilakukan pada hari kerja (Senin-Jumat). Pembelian setelah jam 16:00 WIB akan diproses pada hari kerja berikutnya.
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title mb-4">Lokasi Kami</h3>
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126715.81349545462!2d110.33964593582938!3d-7.782945793101639!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a59a87bc3c57d%3A0xe9756b165b20a13!2sYogyakarta%2C%20Yogyakarta%20City%2C%20Special%20Region%20of%20Yogyakarta!5e0!3m2!1sen!2sid!4v1651225084781!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection