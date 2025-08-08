<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostAddRequest;
use App\Http\Resources\Api\PostResource;
use App\Models\FavoritePost;
use App\Models\Post;
use App\Trait\PaginatesWithOffsetTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use PaginatesWithOffsetTrait;
    public function __construct(
        public readonly Post $post,
        public readonly FavoritePost $favoritePost
    ){}
    public function getList(Request $request):array
    {
        $limit =    $request['limit']??10;
        $offset =    $request['offset']??1;
        $this->resolveOffsetPagination(offset: $request['offset']);

        $filter = [
          'category_id' => $request['category_id']??null,
          'subcategory_id' => $request['subcategory_id']??null,
          'sub_subcategory_id' => $request['sub_subcategory_id']??null,
          'location'=>$request['location']??null,
          'userId'=>$request->user()->id??null,
        ];
        $posts = $this->post->select([
                'id',
                'title',
                'description',
                'price',
                'work_type',
                'payment_type',
            ])
            ->with(['locations'])->getListByFilter(filter:$filter)->paginate($limit);
        return $this->paginatedResponse(collection: $posts, resourceClass: PostResource::class, limit: $limit,offset: $offset, key:'posts');
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

    public function getAllList(Request $request):array
    {
        $limit =    $request['limit']??10;
        $offset =    $request['offset']??1;
        $this->resolveOffsetPagination(offset: $request['offset']);
        $filter = [
            'category_id' => $request['category_id']??null,
            'subcategory_id' => $request['subcategory_id']??null,
            'sub_subcategory_id' => $request['sub_subcategory_id']??null,
            'location'=>$request['location']??null,
        ];
        $posts = $this->post->select([
            'id',
            'user_id',
            'title',
            'description',
            'price',
            'work_type',
            'payment_type',
        ])
            ->with(['locations','user'])->getListByFilter(filter:$filter)->paginate($limit);
        return $this->paginatedResponse(collection: $posts, resourceClass: PostResource::class, limit: $limit,offset: $offset, key:'posts');
    }

    public function addFavorite(Request $request):JsonResponse
    {
        $validate = Validator::make($request->all(),[
            'post_id' => 'required|uuid|exists:posts,id',
        ]);
        if($validate->fails()){
            return response()->json(['message' => $validate->errors()], 422);
        }
        $user = $request->user();
        $this->favoritePost->create([
            'post_id' => $request['post_id'],
            'user_id' => $user['id'],
        ]);
        return response()->json(['message' => 'Post added to favorite list.'], 200);
    }

}
