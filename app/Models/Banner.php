<?php

namespace App\Models;

use App\Trait\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use FileManagerTrait;
    protected $fillable = [
        'title',
        'type',
        'url',
        'image',
        'status',
    ];
    public function ScopeActive($query):Builder
    {
        return $query->where('status', 1);
    }
    protected $appends = ['image_url'];
    public function getImageURLAttribute(): string|null
    {
        if (!$this['image']) {
            return null;
        }
        $storage = config('filesystems.disks.default') ?? 'public';
        return Storage::disk($storage)->url('banner'.$this['image']);
    }
    protected static function boot():void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->image = self::uploadFileOrImage(dir:'banner',image: $model->image);
        });
        static::updating(function ($model) {
            $oldImage = $model->getOriginal('image');
            $model->image = self::updateFileOrImage(dir:'banner',oldImage:$oldImage ,image: $model->image);
        });
    }
}
