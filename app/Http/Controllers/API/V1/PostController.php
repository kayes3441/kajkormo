<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\PostAddRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct(
        public readonly Post $post
    ){}
    public function getList(Request $request):JsonResponse
    {
        $posts = $this->post->with(['locations'])->where('user_id', $request->user()->id)->get();
        return response()->json($posts);
    }
    public function add(PostAddRequest $request): JsonResponse
    {
        $user = $request->user();
        $post = $this->post->create([
            'user_id'           => $user['id'],
            'title'             => $request['title'],
            'description'       => $request['description'],
            'category_id'       => $request['category_id'],
            'subcategory_id'    => $request['subcategory_id'],
            'sub_subcategory_id'=> $request['sub_subcategory_id'],
            'price'             => $request['price'] ?? 0,
            'work_type'         => $request['work_type'],
            'payment_type'      => $request['payment_type'],
            'published_at'      => now(),
        ]);
        foreach ($request->input('location', []) as $level => $locationId) {
            $post->locations()->attach($locationId, ['level' => $level]);
        }
        return response()->json(['message' => 'Post Created successfully.'],200);
    }

}
