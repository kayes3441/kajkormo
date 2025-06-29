<?php

namespace App\Utils;
use App\Models\Setting;




if (!function_exists('getConfigurationData')) {
    function getConfigurationData($name): string|object|array|null
    {
        $check = [
            'web_header_logo',
            'web_footer_logo',
            'web_fav_icon',
            'web_loading_gif',
            'app_header_logo',
            'panel_primary_color',
        ];
        if (in_array($name, $check) === true && session()->has($name)) {
            $config = session($name);
        }else {
            $config = Setting::get($name);
            if (in_array($name, $check) === true) {
                session()->put($name, $config);
            }
        }
        return $config;
    }
}

