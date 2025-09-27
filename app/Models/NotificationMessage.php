<?php

namespace App\Models;

use App\Trait\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class NotificationMessage extends Model
{
    use HasTranslations;

    protected $fillable = ['message'];

    public $incrementing = false;
    protected $keyType = 'string';


}
