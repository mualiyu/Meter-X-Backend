<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'website',
        'api_endpoint',
        'api_key',
        'api_secret_key',
        'api_public_key',
    ];


}
