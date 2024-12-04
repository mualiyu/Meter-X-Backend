<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        // 'user_id',
        'name',
        'address',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'otp',
        'is_online'

        // '',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public static function getLatestCustomers($limit = 4)
    {
        return self::orderBy('created_at', 'desc')->take($limit)->get();
    }
}
