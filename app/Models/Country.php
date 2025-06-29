<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $keyType = 'string';
    protected $fillable = [
        'status',
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

}
