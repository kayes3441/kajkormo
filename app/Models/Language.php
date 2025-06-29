<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
class Language extends Model
{
    protected $fillable = [
        'name', 'code', 'direction', 'status', 'default_status'
    ];
    protected $casts = [
        'id' =>'string'
    ];
    public static function boot():void
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->default) {
                static::where('id', '!=', $model->id)->update(['default_status' => false]);
            }
        });
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
        static::deleting(function ($model) {
            if ($model->getKey() && $model->code != 'EN') {
                $dir = base_path('resources/lang/' . $model->code);
                if (File::isDirectory($dir)) {
                    $iterator = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::CHILD_FIRST
                    );

                    foreach ($iterator as $file) {
                        if ($file->isDir()) {
                            @rmdir($file->getRealPath());
                        } else {
                            @unlink($file->getRealPath());
                        }
                    }
                    @rmdir($dir);
                }
            }
        });
    }
}
