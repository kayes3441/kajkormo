<?php

use App\Http\Controllers\Api\V1\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\V1\Auth\UserAPIAuthController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ReviewPostController;
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
    Route::group(['prefix' => 'forget-password',], function () {
        Route::controller( ForgetPasswordController::class)->group(function () {
            Route::post('reset-request', 'resetPasswordRequest');
            Route::post('otp-verify', 'OtpVerification');
            Route::patch('change-password', 'changePassword');
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
        Route::get('details', 'getDetails');
    });

    Route::prefix('review-post')->controller(ReviewPostController::class)->group(function () {
        Route::get('list', 'getList');

    });
    Route::prefix('banner')->controller(BannerController::class)->group(function () {
        Route::get('list', 'getList');

    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::prefix('profile')
            ->controller(ProfileController::class)
            ->group(function () {
                Route::get('info', 'getInfo');
                Route::patch('update-info', 'updateInfo');
                Route::patch('update-password', 'updatePassword');
                Route::patch('update-location', 'updateLocation');
                Route::patch('change-language', 'changeLanguage');

            });
        Route::prefix('post')->controller(PostController::class)->group(function () {
            Route::get('list', 'getList');
            Route::post('add', 'add');
            Route::patch('update', 'updatePost');

            Route::post('add-favorite', 'addFavorite');
            Route::get('favorite-post-list', 'getFavoritePostList');
            Route::delete('remove-favorite', 'removeFavorite');
            Route::delete('delete', 'deletePost');
        });
        Route::prefix('review-post')->controller(ReviewPostController::class)->group(function () {
            Route::post('add', 'add');
            Route::patch('update', 'update');
            Route::delete('delete', 'delete');
        });

        Route::prefix('chat')->controller(ChatController::class)->group(function () {
            Route::get('list', 'getList');
            Route::get('details', 'getDetails');
            Route::post('add', 'add');
            Route::patch('read', 'read');
        });
    });


});
