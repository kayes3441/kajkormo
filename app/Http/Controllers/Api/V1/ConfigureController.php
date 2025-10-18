<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ConfigureResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigureController extends Controller
{
    public function getList():JsonResponse
    {

        return ConfigureResource::collection();
    }
}
