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
            $code = 'en';
            $direction = 'ltr';
            session()->put('local', $code);
            Session::put('direction', $direction);
            $lang = $code;
        }
        return $lang;
    }
    public static function validationErrorProcessor($validator): array
    {
        $errorKeeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $errorKeeper[] = ['code' => $index, 'message' => $error[0]];
        }
        return $errorKeeper;
    }
}
