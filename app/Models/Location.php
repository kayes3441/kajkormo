<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Str;

class Location extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'level',
        'country_code',
        'latitude',
        'longitude',
        'parent_id',
        'status',
    ];
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->slug = Str::slug($model->name . '-' . $model->level);
        });

        static::updating(function ($model) {
            if ($model->isDirty(['name', 'level'])) {
                $model->slug = Str::slug($model->name . '-' . $model->level);
            }
        });
    }
}
