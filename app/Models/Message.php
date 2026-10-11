<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'body',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Semua pesan antara 2 user (dua arah).
     */
    public function scopeBetween(Builder $q, int $userA, int $userB): Builder
    {
        return $q->where(function (Builder $qq) use ($userA, $userB) {
            $qq->where('sender_id', $userA)->where('receiver_id', $userB);
        })->orWhere(function (Builder $qq) use ($userA, $userB) {
            $qq->where('sender_id', $userB)->where('receiver_id', $userA);
        });
    }
}
