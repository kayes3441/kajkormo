<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLocation;
use App\Trait\FileManagerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use FileManagerTrait;
    public function __construct(
        public readonly User $user,
    ){}

    public function getInfo(Request $request):JsonResponse
    {
        $user = $request->user();
        $user = $this->user->find($user['id']);
        return response()->json($user);
    }
    public function updateInfo(Request $request): JsonResponse
    {
        $user = $request->user();
        $validator = Validator::make($request->all(),[
            'first_name'        => 'required|string|max:50',
            'last_name'         => 'required|string|max:50',
            'address'           =>  'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user['id']),
            ],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $this->user->find($user['id'])->update([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'image' => $this->updateFileOrImage(dir: 'profile',oldImage: $user['image'],image: $request['image']),
        ]);
        return response()->json(['message' => 'Profile updated successfully.'],200);
    }
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'password'       => 'required|min:8|confirmed|different:current_password',
            'old_password'       => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = $request->user();
        if (! $user || ! Hash::check($request['old_password'], $user['password'])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $this->user->find($user['id'])->update([
            'password'       => bcrypt($request['password']),
        ]);
        return response()->json(['message' => 'Password updated successfully.'],200);
    }
    public function updateLocation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'location'      =>'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = $request->user();
        foreach ($request['location'] as $key=>$locationId) {
            UserLocation::where(['user_id' => $user['id'],'level' => $key])->update([
                'location_id' => $locationId,
            ]);
        }
        return response()->json(['message' => 'Location updated successfully.'],200);
    }

    public function changeLanguage(Request $request): JsonResponse
    {
        $user = $request->user();
        $validator = Validator::make($request->all(),[
            'app_language'        => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $this->user->find($user['id'])->update([
            'app_language' => $request['app_language'],
        ]);
        return response()->json(['message' => 'Location updated successfully.'],200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (! $user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $user->forceDelete();

            return response()->json(['message' => 'User deleted successfully.'], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Failed to delete user.',
                'error'   => $exception->getMessage(),
            ], 500);
        }
    }
    public function updateDeviceToken(Request $request): JsonResponse
    {
        $user = $request->user();
        $validator = Validator::make($request->all(),[
            'device_token'        => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $this->user->find($user['id'])->update([
            'device_token' => $request['device_token'],
        ]);
        return response()->json(['message' => 'Device Token updated successfully.'],200);
    }
}
