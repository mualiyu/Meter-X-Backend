<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumptionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'voltage',
        'current',
        'power',
        'is_power_active', //I.e., if there's power in the 🏡 (Nepa or Grid)
        'date',
        'time'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
