<?php

namespace App\Http\Controllers\API\v1\Auth;


use App\Http\Controllers\Controller;
use App\Models\OtpVerificationCode;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use function Laravel\Prompts\error;

class UserAPIAuthController extends Controller
{
    public function __construct(
    )
    {
    }

    public function registration(Request $request): JsonResponse
    {
        $request->validate([
            'f_name'        => 'required|string|max:50',
            'l_name'        => 'required|string|max:50',
            'email'         => 'email|unique:users',
            'phone'         => 'required|unique:users',
            'gender'        => 'required|in:male,female,other',
            'password'      => 'required|min:8|confirmed_password',
            'location'      =>'required|array',
        ]);

        $temporaryToken = Str::uuid()->toString();
        $user = User::create([
            'f_name'         => $request['f_name'],
            'l_name'         => $request['l_name'],
            'email'          => $request['email'],
            'phone'          => $request['phone'],
            'gender'         => $request['gender'],
            'password'       => bcrypt($request['password']),
            'temporary_token'=> $temporaryToken,
        ]);
        foreach ($request['location'] as $key=>$locationId) {
            UserLocation::create([
                'user_id' => $user['id'],
                'level' => $key,
                'location_id' => $locationId,
            ]);
        }
        $code = $this->OTPGenerate(clientID: $user['id']);
        return response()->json([
            'temporary_token' => $temporaryToken,
            'code' => $code,
        ], 201);
    }

    public function verifyPhone(Request $request): JsonResponse
    {
        $request->validate([
            'temporary_token' => 'required|uuid',
            'otp'             => 'required|digits:6',
        ]);
        $user = User::where('temporary_token', $request['temporary_token'])->firstOrFail();
        $checkOTP = $this->checkOtpVerified(clientID:$user['id'],OTPCode:$request['code']);
        if ($checkOTP['status'] === 'error') {
            return response()->json($checkOTP);
        }
        $user->forceFill([
            'phone_verified_at' => now(),
            'temporary_token'   => null,
        ])->save();
        $accessToken = $user->createToken('apiToken')->accessToken;
        return response()->json([
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
        ]);
    }

    protected function checkOTPVerified($clientID,$OTPCode):array
    {
       $getOTPCode =  OtpVerificationCode::where('client_id', $clientID)->first();
       if($getOTPCode === $OTPCode){
           $getOTPCode->delete();
           return [
               'status' => 200,
               'message' => 'OTP Verified Successfully'
           ];
       }
        return [
            'status' => 403,
            'message' => 'OTP code not matched'
        ];
    }

    public function login(Request $request):JsonResponse
    {
        $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);

        $user = User::where('phone', $request['phone'])->first();

        if (! $user || ! Hash::check($request['password'], $user['password'])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        if (is_null($user->phone_verified_at)) {
            $temporaryToken = Str::uuid()->toString();
            $code = $this->OTPGenerate(clientID: $user['id']);
            return response()->json(['message' => 'Phone not verified', 'temporary_token' => $temporaryToken,'code' => $code,], 403);
        }
        $token = $user->createToken('mobile')->accessToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }
    
    protected function OTPGenerate($clientID):int
    {
        OtpVerificationCode::where('client_id', $clientID)->delete();
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        OtpVerificationCode::create([
            'client_id' => $clientID,
            'channel' => 'sms',
            'context' => 'sign_up',
            'code' =>$code,
        ]);
        return $code;
    }

}
