<?php

namespace App\Models;

use App\Trait\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{

    protected $fillable = [
        'title',
        'type',
        'url',
        'image',
        'status',
    ];

}
