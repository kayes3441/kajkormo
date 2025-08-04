<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Locatable extends Model
{
    protected $fillable = [
        'locatable_id',
        'locatable_type',
        'level',
        'location_id',
    ];


    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'locatable');
    }

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'locatable');
    }

}
