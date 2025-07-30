<?php

use App\Http\Controllers\API\V1\Auth\UserAPIAuthController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\LocationController;
use App\Http\Controllers\API\V1\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test',function (Request $request){
   return 'okk';
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::group([  'prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth',], function () {
        Route::controller( UserAPIAuthController::class)->group(function () {
            Route::post('registration', 'registration');
            Route::get('otp-verify', 'verifyOTP');
            Route::get('login', 'login');
            Route::get('logout', 'logout')->middleware('auth:api');
        });
    });
    Route::prefix('location')
        ->controller(LocationController::class)->group(function () {
            Route::get('list', 'getList');

    });
    Route::prefix('category')
        ->controller(CategoryController::class)->group(function () {
            Route::get('list', 'getList');

        });
    Route::prefix('profile')
        ->middleware(['auth:api'])
        ->controller(ProfileController::class)
        ->group(function () {
            Route::get('info', 'getInfo');
            Route::post('update-info', 'updateInfo');
            Route::get('update-password', 'updatePassword');
            Route::get('update-location', 'updateLocation');
        });
});
