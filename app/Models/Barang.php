<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'ID_BARANG';
    public $timestamps = false;

    protected $fillable = [
        'ID_PEMBELI',
        'ID_PENITIPAN',
        'ID_KATEGORI',
        'ID_TRANSAKSI',
        'ID_DONASI',
        'ID_ORGANISASI',
        'ID_PEGAWAI',  
        'NAMA_BARANG',
        'DESKRIPSI',
        'KATEGORI',
        'HARGA',
        'STATUS',
        'GARANSI',
        'stok',  
        'tanggal_garansi',
        'foto_produk',
        'foto_produk2'
    ];

    protected $dates = [
        'tanggal_garansi'
    ];

    protected $casts = [
        'ID_KATEGORI' => 'integer',
        'HARGA' => 'decimal:2',
        'stok' => 'integer',
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    public function penitipan()
    {
        return $this->belongsTo(Penitipan::class, 'ID_PENITIPAN', 'ID_PENITIPAN');
    }

    public function penitipanAktif()
    {
        return $this->belongsTo(Penitipan::class, 'ID_PENITIPAN', 'ID_PENITIPAN')
                    ->where('STATUS_PENITIPAN', 'Aktif');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'ID_KATEGORI', 'ID_KATEGORI');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function donasi()
    {
        return $this->belongsTo(Donasi::class, 'ID_DONASI', 'ID_DONASI');
    }

    public function keranjangPembeli()
    {
        return $this->hasMany(KeranjangPembeli::class, 'ID_BARANG', 'ID_BARANG');
    }
    
    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'ID_ORGANISASI', 'ID_ORGANISASI');
    }

    public function diskusi()
    {
        return $this->hasMany(Diskusi::class, 'ID_BARANG', 'ID_BARANG');
    }
    
    public function diskusiParent()
    {
        return $this->hasMany(Diskusi::class, 'ID_BARANG', 'ID_BARANG')
                    ->whereNull('ID_PARENT')
                    ->orderBy('created_at', 'desc');
    }

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'ID_PENITIP', 'ID_PENITIP');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function isAvailable()
    {
        return $this->STATUS == 'Tersedia' && $this->stok > 0;
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->HARGA, 0, ',', '.');
    }

    public function scopePenitipanAktif($query)
    {
        return $query->whereHas('penitipan', function($q) {
            $q->where('STATUS_PENITIPAN', 'Aktif');
        });
    }

    public function scopeWithoutPenitipanAktif($query)
    {
        return $query->where(function($q) {
            $q->whereNull('ID_PENITIPAN')
              ->orWhereHas('penitipan', function($subq) {
                  $subq->where('STATUS_PENITIPAN', '!=', 'Aktif');
              });
        });
    }

    public function markAsDonated($organisasiId)
    {
        try {
            $this->STATUS = 'Donasi';
            $this->ID_ORGANISASI = $organisasiId;
            $this->save();
            
            // Also update related penitipan if exists
            if ($this->penitipan) {
                $this->penitipan->markAsDonated();
            }
            
            \Log::info("Barang ID {$this->ID_BARANG} marked as donated to organization {$organisasiId}");
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Error marking barang {$this->ID_BARANG} as donated: " . $e->getMessage());
            return false;
        }
    }

    public function isAvailableForDonation()
    {
        return $this->STATUS === 'Tersedia' && 
            is_null($this->ID_ORGANISASI) && 
            is_null($this->ID_PEMBELI);
    }

    // Add this scope to your Barang model
    public function scopeAvailableForDonation($query)
    {
        return $query->where('STATUS', 'Tersedia')
                    ->whereNull('ID_ORGANISASI')
                    ->whereNull('ID_PEMBELI');
    }

    public function getFotoBarangAttribute()
    {
        $foto = [];

        if ($this->foto_produk) {
            $foto[] = $this->foto_produk;
        }

        if ($this->foto_produk2) {
            $foto[] = $this->foto_produk2;
        }

        if (empty($foto)) {
            $foto[] = 'default.jpg'; // nama file default di folder public/images/fotoProduk/
        }

        return collect($foto);
    }

    public function scopeAvailableForPenitipan($query)
    {
        return $query->where('STATUS', '!=', 'Terjual')
                    ->where(function($q) {
                        $q->whereNull('ID_PENITIPAN')
                          ->orWhere(function($subq) {
                              $subq->whereHas('penitipan', function($penitipanQuery) {
                                  $penitipanQuery->where('STATUS_PENITIPAN', '!=', 'Aktif');
                              });
                          });
                    });
    }

    public function scopeAvailableForPenitipanSimple($query)
    {
        return $query->where('STATUS', '!=', 'Terjual')
                    ->where(function($q) {
                        $q->whereNull('ID_PENITIPAN')
                          ->orWhereNotExists(function($subQuery) {
                              $subQuery->select(\DB::raw(1))
                                      ->from('penitipan')
                                      ->whereColumn('penitipan.ID_PENITIPAN', 'barang.ID_PENITIPAN')
                                      ->where('penitipan.STATUS_PENITIPAN', 'Aktif');
                          });
                    });
    }
}
