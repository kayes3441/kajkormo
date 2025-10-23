<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserIdentityResource;
use App\Models\UserIdentity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserIdentityController extends Controller
{
    public function getIdentity(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = $request->user_id ?? $user?->id;

        if (!$userId) {
            return response()->json(['message' => 'User ID is required.'], 400);
        }

        $identity = UserIdentity::where(['user_id'=> $userId])->first();

        if (!$identity) {
            return response()->json(['message' => 'No identity found for this user.'], 404);
        }

        return response()->json([
            'message' => 'User identity retrieved successfully.',
            'data' => new UserIdentityResource($identity),
        ]);
    }


    public function update(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required|exists:users,id',
            'identity_type' => 'required|in:nid,passport,driving_license',
            'front_image' => 'nullable|image|max:2048',
            'back_image' => 'nullable|image|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = $request->user();
        $front = null;
        $back = null;
        if ($request->hasFile('front_image')) {
            $front = $request->file('front_image')->store('identities', 'public');
        }

        if ($request->hasFile('back_image')) {
            $back = $request->file('back_image')->store('identities', 'public');
        }

        $images = [
            'front' => $front,
            'back' => $back,
        ];

        $inserted = DB::table('user_identities')->updateOrInsert(
            ['user_id' => $user['id']],
            [
                'identity_type' => $request['identity_type'],
                'images' => json_encode($images),
                'status' => 'pending',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json([
            'message' => $inserted
                ? 'User identity saved successfully.'
                : 'Failed to save user identity.',
            'data' => new UserIdentityResource(
                UserIdentity::where('user_id', $user['id'])->first()
            ),
        ]);
    }

}
