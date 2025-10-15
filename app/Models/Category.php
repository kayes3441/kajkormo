<?php

namespace App\Models;

use App\Trait\FileManagerTrait;
use App\Trait\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class Category extends Model
{
    use HasFactory, SoftDeletes,HasTranslations,FileManagerTrait;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'slug', 'level', 'parent_id','priority','icon', 'status'];

    protected static function boot():void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
            $model->slug = Str::slug($model->name);
        });
        static::deleting(function ($category) {
            $category->translations()->delete();
        });
    }
    public function getDefaultName($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations()
            ->where('key', 'name')
            ->where('locale', $locale)
            ->value('value')
            ?? $this->name;
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    protected $appends = ['icon_url'];
    public function getImageURLAttribute(): string|null
    {
        if (!$this['icon']) {
            return null;
        }
        $storage = config('filesystems.disks.default') ?? 'public';
        return Storage::disk($storage)->url($this['icon']);
    }
}
