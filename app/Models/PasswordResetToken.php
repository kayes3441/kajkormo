<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PasswordResetToken extends Model
{
    protected $table = "password_reset_tokens";
    protected $fillable =
        [
            'client_id',
            'temporary_token',
            'channel',
            'token',
            'created_at',
            'updated_at',
        ];

    protected static function boot():void
    {
        parent::boot();
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
