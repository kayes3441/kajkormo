<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgetPasswordController extends Controller
{
    public function __construct(
        public readonly User $user
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
        $user = $this->user->where(['phone'=>$request])->first();
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $passwordVerificationToken = PasswordResetToken::where(['user_id'=>$user['id']])->first();
        $otpIntervalTime = 200;
        if(isset($passwordVerificationToken) &&  Carbon::parse($passwordVerificationToken['created_at'])->diffInSeconds() < $otpIntervalTime){
            $time= $otpIntervalTime - Carbon::parse($passwordVerificationToken['created_at'])->diffInSeconds();
            return response()->json(['message' =>'Please Try Again After'.' '.CarbonInterval::seconds($time)->cascade()->forHumans()], 200);
        }
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        if($passwordVerificationToken){
            $passwordVerificationToken->update(['token'=>$token,'created_at'=>now(),'updated_at'=>now()]);
        }else{
            PasswordResetToken::create([
                'user_id'=>$user['id'],
                'token'=>$token,
            ]);
        }

        return response()->json([
            'message' => 'OTP Sent Successfully',
            'resend_time'=> $otpIntervalTime,
            'otp'=>$token
        ], 200);
    }

    public function OTPVerification(Request $request):JsonResponse
    {

    }
}
