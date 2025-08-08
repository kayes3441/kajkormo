<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReviewPostAddRequest;
use App\Models\ReviewPost;
use Illuminate\Http\JsonResponse;

class ReviewPostController extends Controller
{
    public function __construct(
        public readonly ReviewPost $reviewPost
    ){

    }
    public function add(ReviewPostAddRequest $request):JsonResponse
    {
        $user = $request->user();
        $this->reviewPost->create([
           'user_id' => $user['id'],
           'post_id' => $request['post_id'],
           'rating' => $request['rating'],
           'comment' => $request['comment'],
        ]);
        return response()->json(['message' => 'Review added successfully.'],200);
    }
}
