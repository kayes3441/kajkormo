<?php

use App\Http\Controllers\Api\ReviewPostController;
use App\Http\Controllers\Api\V1\Auth\UserAPIAuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ProfileController;
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
    Route::prefix('post')->controller(PostController::class)->group(function () {
        Route::get('all-list', 'getAllList');
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::prefix('profile')
            ->controller(ProfileController::class)
            ->group(function () {
                Route::get('info', 'getInfo');
                Route::patch('update-info', 'updateInfo');
                Route::patch('update-password', 'updatePassword');
                Route::patch('update-location', 'updateLocation');

            });
        Route::prefix('post')->controller(PostController::class)->group(function () {
            Route::get('list', 'getList');
            Route::post('add', 'add');
            Route::post('add-favorite', 'addFavorite');
        });
        Route::prefix('review-post')->controller(ReviewPostController::class)->group(function () {
            Route::post('add', 'add');
        });

    });
});
