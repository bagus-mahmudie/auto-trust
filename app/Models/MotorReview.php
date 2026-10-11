<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MotorReview extends Model
{
    use HasFactory;

    protected $fillable = ['motor_id', 'user_id', 'name', 'rating', 'comment'];

    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
