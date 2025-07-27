<?php

use App\Http\Controllers\API\V1\Auth\UserAPIAuthController;
use App\Http\Controllers\API\V1\LocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test',function (Request $request){
   return 'okk';
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::group(['namespace' => 'api\v1', 'prefix' => 'V1', 'middleware' => ['api_lang']], function () {
    Route::group(['prefix' => 'auth', 'namespace' => 'auth'], function () {
        Route::controller( UserAPIAuthController::class)->group(function () {
            Route::post('register', 'register');
            Route::post('login', 'login');
            Route::get('logout', 'logout')->middleware('auth:api');
        });
    });
    Route::prefix('location')
        ->namespace('location')
        ->controller(LocationController::class)->group(function () {
            Route::post('list/{params}', 'list');

    });
});
