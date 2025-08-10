<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{
    public function __construct(
        public readonly User $user,
        public readonly PasswordResetToken $passwordResetToken
    )
    { }

    public function resetPasswordRequest(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'phone' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = $this->user->where(['phone'=>$request['phone']])->first();
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $passwordVerificationToken = $this->passwordResetToken->where(['client_id'=>$user['id']])->first();
        $otpIntervalTime = 200;
        if(isset($passwordVerificationToken) &&  Carbon::parse($passwordVerificationToken['created_at'])->diffInSeconds() < $otpIntervalTime){
            $time= $otpIntervalTime - Carbon::parse($passwordVerificationToken['created_at'])->diffInSeconds();
            return response()->json(['message' =>'Please Try Again After'.' '.CarbonInterval::seconds($time)->cascade()->forHumans()], 200);
        }
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $temporaryToken = Str::uuid()->toString();
        if($passwordVerificationToken){
            $passwordVerificationToken->update(['temporary_token' => $temporaryToken,'token'=>$token,'created_at'=>now(),'updated_at'=>now()]);
        }else{
            $this->passwordResetToken->create([
                'client_id'=>$user['id'],
                'temporary_token'=>$temporaryToken,
                'channel'=>'sms',
                'token'=>$token,
            ]);
        }

        return response()->json([
            'message' => 'OTP Sent Successfully',
            'resend_time'=> $otpIntervalTime,
            'temporary_token'=> $temporaryToken,
            'token'=>$token
        ], 200);
    }

    public function OtpVerification(Request $request):JsonResponse
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
        $passwordResetToken = $this->passwordResetToken->where(['temporary_token'=> $request['temporary_token']])->first();
        $temporaryToken = Str::uuid()->toString();
        $passwordResetToken->update(['temporary_token' => $temporaryToken]);
        if($passwordResetToken['token'] === $request['token']){
            return response()->json([
                'message'   => 'OTP Verified Successfully',
                'temporary_token'=> $temporaryToken
            ],200);
        }
        return response()->json([
            'message'   => 'OTP Not matched',
        ],400);
    }
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'temporary_token' => 'required|uuid',
            'password'       => 'required|min:8|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $passwordResetToken = $this->passwordResetToken->where(['temporary_token'=> $request['temporary_token']])->first();

        $this->user->find($passwordResetToken['client_id'])->update([
            'password'       => bcrypt($request['password']),
        ]);
        $passwordResetToken->delete();
        return response()->json(['message' => 'Password changed successfully.'],200);
    }

}
