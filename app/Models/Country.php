<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //
    public function scopeActive($query)
    {
        return $query->where('is_active', 1); // adjust column name to match your DB
    }
}
