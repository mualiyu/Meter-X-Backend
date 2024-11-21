<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumptionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'date',
        'voltage',
        'current',
        'power_consumed',
        'hours_with_power',
        'time'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
