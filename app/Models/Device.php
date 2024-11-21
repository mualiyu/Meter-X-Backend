<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'device_name',
        'meter_id',
        'is_online',
        'status',
        'type',
        'price',
        'stock_status',
        'is_linked',

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








