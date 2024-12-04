<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'device_id',
        'is_online',
        'production_date',
        'status',
        'type',
        'price',
        'is_linked',

        'meter_no',
        'meter_type',
        'service_provider',
        // '',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function consumptionHistory()
    {
        return $this->hasMany(ConsumptionHistory::class);
    }
}








