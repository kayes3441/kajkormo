<?php

namespace App\Models;

use App\Trait\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
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
        return Storage::disk($storage)->url($this['image']);
    }
}
