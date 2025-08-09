<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
