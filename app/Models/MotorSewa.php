<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorSewa extends Model
{
    protected $table = 'motor_sewa';

    protected $fillable = [
        'slug','user_id','gambar_motor','galery_motor','lokasi_motor','jenis_kendaraan_motor',
        'jenis_motor','transmisi_motor','jenis_bahan_bakar_motor','merk_motor','model_motor','kondisi_motor',
        'status_pajak_motor','kelengkapan_pajak_motor','kelengkapan_surat_motor','atas_nama_motor','riwayat_servis_motor',
        'judul_iklan_motor','deskripsi_iklan_motor','nama_penjual_motor','email_penjual_motor','no_hp_penjual_motor',
        'likes_motor','tahun_motor','tempat_duduk_motor','kapasitas_mesin_motor','konsumsi_bbm_motor',
        'harga_motor','km_motor','warna_motor','tipe_penjual_motor','nama_bursa_motor',
        'security_motor','exterior_motor','convenience_motor','performance_motor',
    ];

    protected $casts = [
        'galery_motor' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
