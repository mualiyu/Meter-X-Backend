<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Electricity extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'meter_no',
        'meter_type',
        'service_provider',
        'phone',
        'email',
        'amount',
        'unit',
        'status',
        'ref_id',
        'data',
        // '',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }
}
