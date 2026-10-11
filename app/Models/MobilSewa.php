<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobilSewa extends Model
{
    protected $table = 'mobil_sewa';

    protected $fillable = [
        'slug','user_id','gambar_mobil','galery_mobil','lokasi','jenis_kendaraan','jenis_mobil',
        'transmisi_mobil','jenis_bahan_bakar_mobil','merk_mobil','model_mobil','kondisi_mobil',
        'status_pajak','kelengkapan_pajak','kelengkapan_surat','atas_nama','riwayat_servis',
        'judul_iklan','deskripsi_iklan','nama_penjual','email_penjual','no_hp_penjual',
        'likes_mobil','tahun','tempat_duduk_mobil','kapasitas_mesin_mobil','konsumsi_bbm_mobil',
        'harga_mobil','km_mobil','warna_mobil','tipe_penjual','nama_bursa_mobil',
        'security_mobil','exterior_mobil','convenience_mobil','performance_mobil',
    ];

    protected $casts = [
        'galery_mobil' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
