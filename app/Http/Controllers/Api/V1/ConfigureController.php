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
            'maintenance_mode_status',
            'maintenance_mode_start_at',
            'maintenance_mode_end_at',
            'business_name',
            'business_email',
            'business_country',
            'business_phone',
            'address',
            'terms_and_conditions',
            'privacy_policy',
            'sms_config_status'
        ];
        $data = Setting::whereIn('key', $listOfKeyArray)->get();
        return response()->json([
            'success' => true,
            'data' => ConfigureResource::collection($data),
        ]);
    }
}
