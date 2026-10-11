<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KebutuhanPosting extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    protected $fillable = [
        'user_id','nama','kota','judul','merek','tipe_bodi','transmisi','range_harga',
    ];
}
