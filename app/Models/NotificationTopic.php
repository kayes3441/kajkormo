<?php

namespace App\Models;

use App\Trait\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class NotificationTopic extends Model
{
    use HasTranslations;
    protected $fillable = [
        'key', 'title', 'user_type', 'message', 'status'
    ];
}
