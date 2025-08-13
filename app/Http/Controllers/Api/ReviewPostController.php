<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReviewPostAddRequest;
use App\Http\Resources\Api\ReviewPostResource;
use App\Models\ReviewPost;
use App\Trait\PaginatesWithOffsetTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewPostController extends Controller
{
    use PaginatesWithOffsetTrait;
    public function __construct(
        public readonly ReviewPost $reviewPost
    ){

    }
    public function getList():array
    {
        $limit =    $request['limit']??10;
        $offset =    $request['offset']??1;
        $this->resolveOffsetPagination(offset: $request['offset']);
        $reviews = $this->reviewPost->where(['post_id'=>$request['post_id']])->paginate($limit);
        return $this->paginatedResponse(collection: $reviews, resourceClass: ReviewPostResource::class, limit: $limit,offset: $offset, key:'posts');

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
    public function update(ReviewPostAddRequest $request): JsonResponse
    {
        $user = $request->user();

        $review = $this->reviewPost->where([
            'id' => $request['id'],
            'user_id' => $user['id'],
        ])->first();

        if (!$review) {
            return response()->json(['message' => 'Review not found or unauthorized.'], 404);
        }

        $review->update([
            'rating'  => $request['rating'],
            'comment' => $request['comment'],
        ]);

        return response()->json(['message' => 'Review updated successfully.'], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        $user = $request->user();

        $review = $this->reviewPost->where([
            'id' => $request['id'],
            'user_id' => $user['id'],
        ])->first();

        if (!$review) {
            return response()->json(['message' => 'Review not found or unauthorized.'], 404);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.'], 200);
    }
}
