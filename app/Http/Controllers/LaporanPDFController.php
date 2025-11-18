<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\Donasi;
use App\Models\Penitipan;
use App\Models\KategoriBarang;
use App\Models\Penitip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class LaporanPDFController extends Controller
{
    public function dashboard()
    {
        return view('owner.laporan.dashboardLaporan');
    }

    public function penjualanBulanan()
    {
        $tahun = now()->year;
        $dataPerBulan = [];

        for ($i = 1; $i <= 12; $i++) {
            $jumlahTerjual = Transaksi::whereYear('TANGGAL_TRANSAKSI', $tahun)
                ->whereMonth('TANGGAL_TRANSAKSI', $i)
                ->count();

            $totalPenjualan = Transaksi::whereYear('TANGGAL_TRANSAKSI', $tahun)
                ->whereMonth('TANGGAL_TRANSAKSI', $i)
                ->sum('TOTAL_TRANSAKSI');

            $dataPerBulan[] = [
                'jumlah_terjual' => $jumlahTerjual,
                'total_penjualan' => $totalPenjualan,
            ];
        }

        $chartBase64 = $this->generateBarChart($dataPerBulan);
        $pdf = Pdf::loadView('owner.laporan.penjualan-bulanan', compact('dataPerBulan', 'tahun', 'chartBase64'));

        return $pdf->stream('penjualan-bulanan.pdf');
    }

    private function generateBarChart($dataPerBulan)
    {
        $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $values = array_map(fn($d) => $d['total_penjualan'], $dataPerBulan);

        $config = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Penjualan Kotor',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    'data' => $values
                ]]
            ],
            'options' => [
                'scales' => [
                    'yAxes' => [[
                        'ticks' => ['beginAtZero' => true]
                    ]]
                ]
            ]
        ];

        $url = 'https://quickchart.io/chart';
        $query = http_build_query(['c' => json_encode($config)]);
        $chartUrl = "$url?$query";

        // Ambil isi gambar dan ubah jadi base64
        $imageContents = file_get_contents($chartUrl);
        $base64 = base64_encode($imageContents);

        return 'data:image/png;base64,' . $base64;
    }

    public function komisiBulanan(Request $request)
    {
        // Get month and year from request, default to current month/year
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        
        // Get month name in Indonesian
        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
        
        // Get transactions with commission data for the specified month
        $transaksi = Transaksi::with([
            'barang.penitipan.penitip',
            'barang.penitipan.barangHunter', 
            'komisi'
        ])
        ->whereHas('komisi') // Only transactions that have commission
        ->whereYear('TANGGAL_TRANSAKSI', $tahun)
        ->whereMonth('TANGGAL_TRANSAKSI', $bulan)
        ->where('STATUS_TRANSAKSI', 'Selesai') // Only completed transactions
        ->orderBy('TANGGAL_TRANSAKSI', 'asc')
        ->get();

        $tanggalCetak = Carbon::now()->translatedFormat('d F Y');

        $pdf = Pdf::loadView('owner.laporan.komisi-bulanan', compact(
            'transaksi', 
            'bulan', 
            'tahun', 
            'namaBulan',
            'tanggalCetak'
        ));
        
        return $pdf->stream("komisi-bulanan-{$namaBulan}-{$tahun}.pdf");
    }

    public function stokGudang()
    {
        $tanggalHariIni = \Carbon\Carbon::today();

        $barangs = \App\Models\Barang::whereHas('penitipan', function ($query) use ($tanggalHariIni) {
            $query->whereDate('TANGGAL_MULAI', $tanggalHariIni);
        })->with([
            'penitipan.penitip',
            'penitipan.barangHunter'
        ])->get();

        $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.laporan.stok-gudang', compact('barangs', 'tanggalCetak'));
        return $pdf->stream('stok-gudang.pdf');
    }


    public function donasiBarang(Request $request)
    {
        // Get filter parameters
        $tahun = $request->get('tahun', Carbon::now()->year);
        $bulan = $request->get('bulan', null);
        $tanggalMulai = $request->get('tanggal_mulai', null);
        $tanggalAkhir = $request->get('tanggal_akhir', null);

        // Build query
        $query = Donasi::with(['barang.penitipan.penitip', 'organisasi'])
                       ->whereNotNull('ID_BARANG'); // Only donations with assigned items

        // Apply year filter
        $query->whereYear('TANGGAL_DONASI', $tahun);

        // Apply month filter if specified
        if ($bulan) {
            $query->whereMonth('TANGGAL_DONASI', $bulan);
        }

        // Apply date range filter if specified
        if ($tanggalMulai && $tanggalAkhir) {
            $query->whereBetween('TANGGAL_DONASI', [$tanggalMulai, $tanggalAkhir]);
        }

        $donasi = $query->orderBy('TANGGAL_DONASI', 'desc')->get();

        // Format filter info for display
        $filterInfo = $this->formatFilterInfo($tahun, $bulan, $tanggalMulai, $tanggalAkhir);
        $tanggalCetak = Carbon::now()->translatedFormat('d F Y');

        $pdf = Pdf::loadView('owner.laporan.donasi-barang', compact(
            'donasi', 
            'tahun', 
            'bulan', 
            'filterInfo',
            'tanggalCetak'
        ));
        
        $filename = "laporan-donasi-barang-{$tahun}";
        if ($bulan) {
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->format('m');
            $filename .= "-{$namaBulan}";
        }
        
        return $pdf->stream("{$filename}.pdf");
    }

    // public function masaTitipHabis()
    // {
    //     $penitipan = Penitipan::with(['barang', 'penitip'])
    //         ->whereDate('TANGGAL_BERAKHIR', '<', Carbon::today())
    //         ->whereNotIn('STATUS_PENITIPAN', ['selesai', 'donasi'])
    //         ->get();

    //     $tanggalCetak = Carbon::now()->format('Y-m-d');
    //     $pdf = Pdf::loadView('owner.laporan.masa-titip-habis', compact('penitipan', 'tanggalCetak'));

    //     return $pdf->download("laporan-masa-titip-habis-{$tanggalCetak}.pdf");
    // }

    public function masaTitipHabis()
    {
        $penitipan = Penitipan::with(['barang', 'penitip'])
            ->whereDate('TANGGAL_BERAKHIR', '<', Carbon::today())
            ->whereNotIn('STATUS_PENITIPAN', ['selesai', 'donasi'])
            ->get();

        $pdf = Pdf::loadView('owner.laporan.masa-titip-habis', compact('penitipan'));
        return $pdf->stream('masa-titip-habis.pdf');
    }


    public function penjualanKategori()
    {
        $kategori = KategoriBarang::with([
            'barangs.transaksi',
            'barangs.penitipan' // relasi tambahan untuk cek status penitipan
        ])->get();

        $tahun = now()->year;
        $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');

        $pdf = Pdf::loadView('owner.laporan.penjualan-kategori', compact('kategori', 'tahun', 'tanggalCetak'));
        return $pdf->stream('penjualan-kategori.pdf');
    }

    // public function penjualanKategori()
    // {
    //     $kategori = KategoriBarang::with(['barangs.transaksi'])->get();
    //     $tahun = now()->year;
    //     $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');

    //     $pdf = Pdf::loadView('owner.laporan.penjualan-kategori', compact('kategori', 'tahun', 'tanggalCetak'));
    //     return $pdf->stream('penjualan-kategori.pdf');
    // }
    
    // public function penjualanKategori()
    // {
    //     $kategori = KategoriBarang::with(['barangs.transaksi' => function ($query) {
    //         $query->where('STATUS_TRANSAKSI', 'selesai');
    //     }])->get();

    //     $tahun = now()->year;
    //     $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');

    //     $pdf = Pdf::loadView('owner.laporan.penjualan-kategori', compact('kategori', 'tahun', 'tanggalCetak'));
    //     return $pdf->stream('penjualan-kategori.pdf');
    // }

    // public function penjualanKategori()
    // {
    //     $kategori = KategoriBarang::with(['barangs.transaksi'])->get();
    //     $tahun = now()->year;
    //     $tanggalCetak = Carbon::now()->translatedFormat('d F Y');
    //     $fileName = 'penjualan-per-kategori-' . Carbon::now()->format('Y-m-d') . '.pdf';

    //     $pdf = Pdf::loadView('owner.laporan.penjualan-kategori', compact('kategori', 'tahun', 'tanggalCetak'));

    //     return $pdf->download($fileName);
    // }

    // public function penjualanKategori()
    // {
    //     $kategori = KategoriBarang::with(['barangs.transaksi' => function ($query) {
    //         $query->where('STATUS_TRANSAKSI', 'Selesai');
    //     }])->get();

    //     $tahun = now()->year;
    //     $tanggalCetak = Carbon::now()->translatedFormat('d F Y');
    //     $fileName = 'penjualan-per-kategori-' . Carbon::now()->format('Y-m-d') . '.pdf';

    //     $pdf = Pdf::loadView('owner.laporan.penjualan-kategori', compact('kategori', 'tahun', 'tanggalCetak'));

    //     return $pdf->download($fileName);
    // }

    public function requestDonasi(Request $request)
    {
        // Get filter parameters
        $tahun = $request->get('tahun', Carbon::now()->year);
        $bulan = $request->get('bulan', null);
        $tanggalMulai = $request->get('tanggal_mulai', null);
        $tanggalAkhir = $request->get('tanggal_akhir', null);
        $statusFilter = $request->get('status', 'pending'); // pending, all

        // Build query
        $query = Donasi::with(['organisasi', 'pegawai', 'barang']);

        // Apply status filter
        if ($statusFilter === 'pending') {
            $query->whereNull('status'); // Pending requests
        }
        // If 'all', don't add status filter

        // Apply year filter
        $query->whereYear('TANGGAL_DONASI', $tahun);

        // Apply month filter if specified
        if ($bulan) {
            $query->whereMonth('TANGGAL_DONASI', $bulan);
        }

        // Apply date range filter if specified
        if ($tanggalMulai && $tanggalAkhir) {
            $query->whereBetween('TANGGAL_DONASI', [$tanggalMulai, $tanggalAkhir]);
        }

        $requests = $query->orderBy('TANGGAL_DONASI', 'desc')->get();

        // Format filter info for display
        $filterInfo = $this->formatFilterInfo($tahun, $bulan, $tanggalMulai, $tanggalAkhir);
        $tanggalCetak = Carbon::now()->translatedFormat('d F Y');

        $pdf = Pdf::loadView('owner.laporan.request-donasi', compact(
            'requests', 
            'tahun', 
            'bulan', 
            'statusFilter',
            'filterInfo',
            'tanggalCetak'
        ));

        $filename = "request-donasi-{$tahun}";
        if ($bulan) {
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->format('m');
            $filename .= "-{$namaBulan}";
        }
        
        return $pdf->stream("{$filename}.pdf");
    }

    public function transaksiPenitip(Request $request) 
    {
        // Get filter parameters
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $penitipId = $request->get('penitip_id', null);
        
        // Build query untuk penitip
        $query = Penitip::with([
            'penitipan' => function($q) {
                $q->with(['barang' => function($barangQuery) {
                    $barangQuery->with(['transaksi' => function($transaksiQuery) {
                        $transaksiQuery->with('komisi')
                                    ->where('STATUS_TRANSAKSI', 'Selesai');
                    }]);
                }]);
            }
        ]);

        // Filter by specific penitip if provided
        if ($penitipId) {
            $query->where('ID_PENITIP', $penitipId);
        }

        // Ambil semua penitip terlebih dahulu
        $allPenitip = $query->get();
        
        // Filter penitip yang memiliki transaksi pada bulan dan tahun yang ditentukan
        $penitip = $allPenitip->filter(function($p) use ($bulan, $tahun) {
            foreach($p->penitipan as $penitipan) {
                if($penitipan->barang) {
                    $hasTransaksi = \App\Models\Transaksi::where('ID_BARANG', $penitipan->barang->ID_BARANG)
                        ->where('STATUS_TRANSAKSI', 'Selesai')
                        ->whereMonth('TANGGAL_TRANSAKSI', $bulan)
                        ->whereYear('TANGGAL_TRANSAKSI', $tahun)
                        ->exists();
                    
                    if($hasTransaksi) {
                        return true;
                    }
                }
            }
            return false;
        });

        // Get month name
        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
        $tanggalCetak = Carbon::now()->translatedFormat('d F Y');
        
        // Prepare filter info
        $filterInfo = "Tahun: {$tahun} | Bulan: {$namaBulan}";
        if ($penitipId) {
            $selectedPenitip = $allPenitip->where('ID_PENITIP', $penitipId)->first();
            if ($selectedPenitip) {
                $filterInfo .= " | Penitip: {$selectedPenitip->NAMA_PENITIP}";
            }
        }
        
        $pdf = PDF::loadView('owner.laporan.transaksi-penitip', compact(
            'penitip', 
            'bulan', 
            'tahun', 
            'namaBulan',
            'tanggalCetak',
            'filterInfo'
        ));
        
        $filename = "laporan-transaksi-penitip-{$bulan}-{$tahun}";
        if ($penitipId) {
            $filename .= "-penitip-{$penitipId}";
        }
        
        return $pdf->stream("{$filename}.pdf");
    }

    /**
     * Helper method to format filter information for display
     */
    private function formatFilterInfo($tahun, $bulan = null, $tanggalMulai = null, $tanggalAkhir = null)
    {
        $info = "Tahun: {$tahun}";
        
        if ($bulan) {
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
            $info .= " | Bulan: {$namaBulan}";
        }
        
        if ($tanggalMulai && $tanggalAkhir) {
            $mulai = Carbon::parse($tanggalMulai)->translatedFormat('d F Y');
            $akhir = Carbon::parse($tanggalAkhir)->translatedFormat('d F Y');
            $info .= " | Periode: {$mulai} - {$akhir}";
        }
        
        return $info;
    }

    /**
     * Show filter form for donation reports
     */
    public function showDonasiBarangFilter()
    {
        return view('owner.laporan.filter.donasi-barang');
    }

    /**
     * Show filter form for donation requests
     */
    public function showRequestDonasiFilter()
    {
        return view('owner.laporan.filter.request-donasi');
    }

    /**
     * Show filter form for penitip transactions
     */
    public function showTransaksiPenitipFilter()
    {
        $penitips = Penitip::select('ID_PENITIP', 'NAMA_PENITIP')
                          ->orderBy('NAMA_PENITIP')
                          ->get();
        
        return view('owner.laporan.filter.transaksi-penitip', compact('penitips'));
    }
}
