<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OtpVerificationCode extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable =[
        'client_id',
        'channel',
        'context',
        'code',
    ];
    protected static function boot():void
    {
        parent::boot();
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::ulid();
            }
        });
    }
}
