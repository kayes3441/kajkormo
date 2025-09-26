<?php

namespace App\Models;

use App\Trait\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Str;

class Location extends Model
{
    use HasTranslations;
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
    protected $hidden = ['pivot'];
    public function getDefaultName($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations()
            ->where('key', 'name')
            ->where('locale', $locale)
            ->value('value')
            ?? $this->name;
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
        static::deleting(function ($category) {
            $category->translations()->delete();
        });
    }
}
