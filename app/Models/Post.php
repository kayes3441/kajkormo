<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id')->select(['id', 'first_name', 'last_name', 'phone']);
    }
    public function locations(): MorphToMany
    {
        return $this->morphToMany(Location::class, 'locatable')
            ->withTimestamps()->withPivot('level','location_id')
            ->select('locations.id', 'locations.name', 'locations.level');
    }

    public function scopeGetListByFilter($query,array $filter)
    {
        return $query
            ->when(!empty($filter['category_id']), function ($query) use ($filter) {
                return $query->where(['category_id'=> $filter['category_id']]);
            })
            ->when(!empty($filter['subcategory_id']), function ($query) use ($filter) {
                return $query->where(['subcategory_id'=> $filter['subcategory_id']]);
            })
            ->when(!empty($filter['sub_subcategory_id']), function ($query) use ($filter) {
                return $query->where(['sub_subcategory_id'=> $filter['sub_subcategory_id']]);
            })
            ->when(!empty($filter['userId']), function ($query) use ($filter) {
                return $query->where(['user_id'=> $filter['userId']]);
            })
            ->when(!empty($filter['location']), function ($query) use ($filter) {
                foreach ($filter['location'] ?? [] as $level => $levelId) {
                    if (!empty($levelId)) {
                        $query->whereHas('locations', function ($query) use ($level, $levelId) {
                            $query->where('locatables.level', $level)
                                ->where('locatables.location_id', (int) $levelId);
                        });
                    }
                }
            });
    }
}
