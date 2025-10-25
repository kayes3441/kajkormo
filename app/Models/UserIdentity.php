<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserIdentity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'identity_type',
        'images',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $appends = [
        'front_image_url',
        'back_image_url',
    ];
    public function getFrontImageUrlAttribute():string|null
    {
        return isset($this->images['front'])
            ? asset('storage/' . $this->images['front'])
            : null;
    }

    public function getBackImageUrlAttribute():string|null
    {
        return isset($this->images['back'])
            ? asset('storage/' . $this->images['back'])
            : null;
    }

}
