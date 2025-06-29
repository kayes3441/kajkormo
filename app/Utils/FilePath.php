<?php
namespace App\Utils;

if (!function_exists('getAssetPath')) {
    function getAssetPath(string $path): string
    {
        if (DOMAIN_POINTED_DIRECTORY == 'public') {
            $position = strpos($path, 'public/');
            $result = $path;
            if ($position === 0) {
                $result = preg_replace('/public/', '', $path, 1);
            }
        } else {
            $result = $path;
        }
        return asset($result);
    }
}

if (!function_exists('getStoragePath')) {
    function getStoragePath(string $path): string
    {
        if (DOMAIN_POINTED_DIRECTORY == 'public') {
            $result = asset('storage/' . $path);
        }else{
            $result = asset('storage/app/public' . $path);
        }
        return $result;
    }
}

if (!function_exists('getImageOrPlaceholder')) {
    function getImageOrPlaceholder($path,$storageType, $type = null): string
    {
        if ($storageType ==='storage')
        {
            $locationPath = storage_path('app/public/'.$path);
            $givenPath = !empty($path) ? getStoragePath($path) : $path;
        }else{
            $locationPath = public_path($path);
            $givenPath = !empty($path) ? getAssetPath($path) : $path;
        }
        $placeholderMap = [
            'placeholder-basic' => 'images/placeholder/placeholder-1-1.png',
        ];
        if (isset($placeholderMap[$type])) {
            return is_file($locationPath) ? $givenPath : getAssetPath(path: 'public/assets/' . $placeholderMap[$type]);
        }
        return is_file($locationPath) ? $givenPath : getStoragePath(path: 'public/assets/front-end/images/placeholder/placeholder-2-1.png');
    }
}
