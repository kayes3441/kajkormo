<?php

use App\Http\Controllers\API\v1\Auth\PassportAuthController;
use App\Http\Controllers\API\v1\Auth\UserAPIAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test',function (Request $request){
   return 'okk';
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::group(['namespace' => 'api\v1', 'prefix' => 'v1', 'middleware' => ['api_lang']], function () {
    Route::group(['prefix' => 'auth', 'namespace' => 'auth'], function () {
        Route::controller(UserAPIAuthController::class)->group(function () {
            Route::get('logout', 'logout')->middleware('auth:api');
        });

        Route::controller( UserAPIAuthController::class)->group(function () {
            Route::post('register', 'register');
            Route::post('login', 'login');
//            Route::post('check-email', 'checkEmail');
//            Route::post('check-phone', 'checkPhone');
//            Route::post('firebase-auth-verify', 'firebaseAuthVerify');
//            Route::post('firebase-auth-token-store', 'firebaseAuthTokenStore');
//            Route::post('verify-otp', 'verifyOTP');
//            Route::post('verify-email', 'verifyEmail');
//            Route::post('verify-phone', 'verifyPhone');
//            Route::post('registration-with-otp', 'registrationWithOTP');
//            Route::post('existing-account-check', 'existingAccountCheck');
//            Route::post('registration-with-social-media', 'registrationWithSocialMedia');
//            Route::post('forgot-password', 'passwordResetRequest');
        });
    });
});
