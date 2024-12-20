<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_provider',
        'variation_code',
        'phone',
        'amount',
        'status',
        'data_unit',
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
