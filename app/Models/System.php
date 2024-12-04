<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_service_id',
        'system_name',
        'description',
        'status',
    ];

    public function api_service()
    {
        return $this->belongsTo(ApiService::class, 'api_service_id');
    }
}
