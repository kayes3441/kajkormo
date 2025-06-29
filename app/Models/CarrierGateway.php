<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarrierGateway extends Model
{
    protected $fillable = [
        'gateway_values',
        'additional_data',
        'image',
        'status',
    ];

    protected $casts = [
        'id'=>'string',
        'key'=>'string',
        'gateway_values'=>'json',
        'additional_data'=>'json',
        'image'=>'string',
        'status'=>'boolean',
    ];
}
