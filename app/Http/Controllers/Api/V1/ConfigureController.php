<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ConfigureResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigureController extends Controller
{
    public function getList():JsonResponse
    {

        $listOfKeyArray = [
            'web_fav_icon',
            'app_header_logo',
            'business_name'
        ];
        $data = Setting::whereIn('key', $listOfKeyArray)->get();
        return response()->json([
            'success' => true,
            'data' => ConfigureResource::collection($data),
        ]);
    }
}
