<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_provider',
        'phone',
        'amount',
        'status',
        'ref_id',
        // '',
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
