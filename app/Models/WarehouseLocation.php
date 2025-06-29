<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WarehouseLocation extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'location_name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'latitude',
        'longitude',
        'city',
        'zip_code',
        'address',
        'other_address',
        'status',
    ];

    protected static function boot():void
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            $model->user_id = Auth::id();
        });
    }
}
