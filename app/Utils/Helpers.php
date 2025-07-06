<?php

namespace App\Utils;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Helpers
{
    public static function getDefaultLang(): string
    {
        if (strpos(url()->current(), '/api')) {
            $lang = App::getLocale();
        } elseif (session()->has('local')) {
            $lang = session('local');
        } else {
//            $data = 'en';
            $code = 'en';
            $direction = 'ltr';
//            foreach ($data as $ln) {
//                if (array_key_exists('default', $ln) && $ln['default']) {
//                    $code = $ln['code'];
//                    if (array_key_exists('direction', $ln)) {
//                        $direction = $ln['direction'];
//                    }
//                }
//            }
            session()->put('local', $code);
            Session::put('direction', $direction);
            $lang = $code;
        }
        return $lang;
    }

}
