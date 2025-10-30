<?php

namespace App\Http\Controllers\Api\V1\Auth;


use App\Http\Controllers\Controller;
use App\Models\OtpVerificationCode;
use App\Models\User;
use App\Models\UserLocation;
use App\Trait\SMSConfigTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use function App\Utils\getConfigurationData;

class UserAPIAuthController extends Controller
{
    use SMSConfigTrait;
    public function __construct(
    )
    {
    }

    public function registration(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'first_name'        => 'required|string|max:50',
            'last_name'        => 'required|string|max:50',
            'email'         => 'email|unique:users',
            'phone'         => 'required|unique:users',
            'gender'        => 'required|in:male,female,other',
            'password'       => 'required|min:8|confirmed',
            'location'      =>'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $temporaryToken = Str::uuid()->toString();
        $user = User::create([
            'first_name'         => $request['first_name'],
            'last_name'         => $request['last_name'],
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
        $token = $this->OTPGenerate(clientID: $user['id']);
        $smsStatus = getConfigurationData('sms_config_status');
        if ($smsStatus)
        {
            $this->sendSMS($user['phone'], $token);
        }
        return response()->json([
            'temporary_token' => $temporaryToken
        ], 201);
    }

    public function verifyOTP(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'temporary_token' => 'required|uuid',
            'token'             => 'required|digits:6|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = User::where('temporary_token', $request['temporary_token'])->first();
        $checkOTP = $this->checkOtpVerified(clientID:$user['id'],OTPCode:$request['token']);
        if ($checkOTP['status'] === 403) {
            return response()->json($checkOTP,403);
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
       $getOTPCode =  OtpVerificationCode::where(['client_id'=> $clientID])->first();
       if($getOTPCode['token'] === $OTPCode){
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
            $user->forceFill([
                'phone_verified_at' => null,
                'temporary_token'   => $temporaryToken,
            ])->save();
            $token = $this->OTPGenerate(clientID: $user['id']);
            return response()->json(['message' => 'Phone not verified', 'temporary_token' => $temporaryToken,'token' => $token,], 403);
        }
        $accessToken = $user->createToken('apiToken')->accessToken;

        return response()->json([
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
        ]);
    }

    protected function OTPGenerate($clientID):int
    {
        OtpVerificationCode::where('client_id', $clientID)->delete();
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        OtpVerificationCode::create([
            'client_id' => $clientID,
            'channel' => 'sms',
            'context' => 'sign_up',
            'token' =>$token,
        ]);
        return $token;
    }

    public function logout(Request $request):JsonResponse
    {
        if (auth()->check()) {
            auth()->user()->currentAccessToken()->revoke();
            return response()->json(['message' => 'Logged Out Successfully'], 200);
        }
        return response()->json(['message' => 'Logged Out Fail'], 403);
    }

}
