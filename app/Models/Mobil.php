<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Mobil extends Model
{
    protected $table = 'mobil';

    protected $fillable = [
        'user_id',
        'gambar_mobil',
        'galery_mobil',
        'lokasi',

        'jenis_kendaraan',
        'jenis_mobil',
        'transmisi_mobil',
        'jenis_bahan_bakar_mobil',
        'merk_mobil',
        'model_mobil',
        'tahun',
        'kapasitas_mesin_mobil',
        'konsumsi_bbm_mobil',
        'km_mobil',
        'tempat_duduk_mobil',
        'kondisi_mobil',
        'warna_mobil',

        'tipe_penjual',
        'nama_bursa_mobil',

        'status_pajak',
        'kelengkapan_pajak',
        'kelengkapan_surat',
        'atas_nama',
        'riwayat_servis',

        'judul_iklan',
        'deskripsi_iklan',

        'nama_penjual',
        'email_penjual',
        'no_hp_penjual',

        'security_mobil',
        'exterior_mobil',
        'convenience_mobil',
        'performance_mobil',

        'harga_mobil',
        'likes_mobil',
    ];

    protected $casts = [
        'galery_mobil' => 'array', // karena di DB JSON
        'konsumsi_bbm_mobil' => 'decimal:2',
        'tahun' => 'integer',
        'tempat_duduk_mobil' => 'integer',
        'kapasitas_mesin_mobil' => 'integer',
        'harga_mobil' => 'integer',
        'km_mobil' => 'integer',
        'likes_mobil' => 'integer',
        
        'security_mobil' => 'array',
        'exterior_mobil' => 'array',
        'convenience_mobil' => 'array',
        'performance_mobil' => 'array',
    ];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(\App\Models\User::class, 'mobil_likes', 'mobil_id', 'user_id');
    }

    public function likedMobils()
    {
        return $this->belongsToMany(\App\Models\Mobil::class, 'mobil_likes', 'user_id', 'mobil_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::creating(function ($mobil) {
            $mobil->slug = static::uniqueSlug($mobil->merk_mobil, $mobil->model_mobil);
        });

        static::updating(function ($mobil) {
            if ($mobil->isDirty(['merk_mobil', 'model_mobil'])) {
                $mobil->slug = static::uniqueSlug($mobil->merk_mobil, $mobil->model_mobil, $mobil->id);
            }
        });
    }

    public static function uniqueSlug(string $merk, string $model, ?int $ignoreId = null): string
    {
        $base = Str::slug(trim($merk . ' ' . $model));
        $slug = $base;
        $i = 2;

        $q = static::query()->where('slug', $slug);
        if ($ignoreId) $q->where('id', '!=', $ignoreId);

        while ($q->exists()) {
            $slug = $base . '-' . $i;
            $i++;

            $q = static::query()->where('slug', $slug);
            if ($ignoreId) $q->where('id', '!=', $ignoreId);
        }

        return $slug;
    }

}
