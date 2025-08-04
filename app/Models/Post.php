<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

class Post extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category_id',
        'subcategory_id',
        'sub_subcategory_id',
        'price',
        'work_type',
        'payment_type',
        'image',
        'status',
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

    public function locations(): MorphToMany
    {
        return $this->morphToMany(Locatable::class, 'locatable')
            ->withPivot('level')
            ->withTimestamps();
    }
}
