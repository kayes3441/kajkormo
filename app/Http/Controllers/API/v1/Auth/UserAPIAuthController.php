<?php

namespace App\Http\Controllers\API\v1\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        return response()->json([
            'temporary_token' => $temporaryToken
        ], 201);
    }

    public function verifyPhone(Request $request): JsonResponse
    {
        $request->validate([
            'temporary_token' => 'required|uuid',
            'otp'             => 'required|digits:6',
        ]);
        $user = User::where('temporary_token', $request['temporary_token'])->firstOrFail();

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
            return response()->json(['message' => 'Phone not verified', 'temporary_token' => $temporaryToken], 403);
        }

        $token = $user->createToken('mobile')->accessToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

}
