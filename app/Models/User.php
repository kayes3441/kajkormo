<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as UserAuth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class User extends UserAuth
{
    use HasFactory, Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'first_name',
        'last_name',
        'image',
        'email',
        'gender',
        'temporary_token',
        'phone',
        'password',
        'phone_verified_at',
        'address',
        'app_language',
        'device_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    protected $appends = ['image_url'];

    public function getImageURLAttribute(): string|null
    {
        if (!$this['image']) {
            return null;
        }
        $storage = config('filesystems.disks.default') ?? 'public';
        return Storage::disk($storage)->url('profile/'.$this['image']);
    }

    protected static function boot():void
    {
        parent::boot();
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function userIdentity():HasOne
    {
        return $this->hasOne(UserIdentity::class);
    }
}
