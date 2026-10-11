<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Motor extends Model
{
    use HasFactory;

    protected $table = 'motor';

    protected $fillable = [
    'user_id',
    'gambar_motor','galery_motor',
    'lokasi_motor',
    'jenis_kendaraan_motor','jenis_motor','transmisi_motor','jenis_bahan_bakar_motor',
    'merk_motor','model_motor','kondisi_motor',
    'status_pajak_motor','kelengkapan_pajak_motor','kelengkapan_surat_motor','atas_nama_motor','riwayat_servis_motor',
    'judul_iklan_motor','deskripsi_iklan_motor',
    'nama_penjual_motor','email_penjual_motor','no_hp_penjual_motor',
    'likes_motor',
    'tahun_motor','tempat_duduk_motor','kapasitas_mesin_motor','konsumsi_bbm_motor','harga_motor','km_motor','warna_motor',
    'tipe_penjual_motor','nama_bursa_motor',
    'security_motor','exterior_motor','convenience_motor','performance_motor',
    ];

    protected $casts = [
        'galery_motor' => 'array',
    ];

    public function getGaleryMotorArrayAttribute(): array
    {
        $raw = $this->galery_motor;
        if (!$raw) return [];

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\MotorReview::class, 'motor_id');
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'motor_likes', 'motor_id', 'user_id')
            ->withTimestamps();
    }

    public function likedMotors()
    {
        return $this->belongsToMany(\App\Models\Motor::class, 'motor_likes', 'user_id', 'motor_id');
    }

     public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::creating(function ($motor) {
            // isi slug saat create
            $motor->slug = static::uniqueSlug($motor->merk_motor, $motor->model_motor);
        });

        static::updating(function ($motor) {
            // update slug kalau merk/model berubah
            if ($motor->isDirty(['merk_motor', 'model_motor'])) {
                $motor->slug = static::uniqueSlug($motor->merk_motor, $motor->model_motor, $motor->id);
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
