<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewPost extends Model
{
    protected $fillable = [
      'id',
      'user_id',
      'post_id',
      'parent_id',
      'rating',
      'comment'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id')->select(['id', 'first_name', 'last_name', 'phone']);
    }
}
