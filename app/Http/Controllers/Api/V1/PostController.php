<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostAddRequest;
use App\Http\Resources\Api\PostResource;
use App\Models\FavoritePost;
use App\Models\Post;
use App\Trait\FileManagerTrait;
use App\Trait\PaginatesWithOffsetTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use PaginatesWithOffsetTrait ,FileManagerTrait;
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
                'images',
                'created_at',
                'updated_at',
            ])
            ->with(['locations'])
            ->getListByFilter(filter:$filter)->paginate($limit);
        return $this->paginatedResponse(collection: $posts, resourceClass: PostResource::class, limit: $limit,offset: $offset, key:'posts');
    }
    public function add(PostAddRequest $request): JsonResponse
    {
        $user = $request->user();
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $this->upload(dir:'post',image: $image);
                $images[] = $path;
            }
        }
        $post = $this->post->create([
            'user_id'           => $user['id'],
            'title'             => $request['title'],
            'description'       => $request['description'],
            'category_id'       => $request['category_id'],
            'subcategory_id'    => $request['subcategory_id'],
            'sub_subcategory_id'=> $request['sub_subcategory_id'],
            'price'             => $request['price'] ?? 0,
            'images'             => $images,
            'work_type'         => $request['work_type'],
            'payment_type'      => $request['payment_type'],
            'published_at'      => now(),
        ]);
        foreach ($request->input('location', []) as $level => $locationId) {
            $post->locations()->attach($locationId, ['level' => $level]);
        }
        return response()->json(['message' => 'Post Created successfully.'],200);
    }
    public function updatePost(PostAddRequest $request, int $id): JsonResponse
    {
        $user = $request->user();

        $post = $this->post->where('id', $id)->where('user_id', $user['id'])->first();

        if (!$post) {
            return response()->json(['message' => 'Post not found or unauthorized.'], 404);
        }

        $images = $post->images ?? [];

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $index=>$image) {
                $oldImage = $images[$index] ?? null;
                $path = $this->update(dir: 'post',oldImage:$oldImage,image: $image);
                $images[] = $path;
            }
        }

        $post->update([
            'title'              => $request['title']       ?? $post->title,
            'description'        => $request['description'] ?? $post->description,
            'category_id'        => $request['category_id'] ?? $post->category_id,
            'subcategory_id'     => $request['subcategory_id'] ?? $post->subcategory_id,
            'sub_subcategory_id' => $request['sub_subcategory_id'] ?? $post->sub_subcategory_id,
            'price'              => $request['price'] ?? $post->price,
            'images'             => $images,
            'work_type'          => $request['work_type']   ?? $post->work_type,
            'payment_type'       => $request['payment_type'] ?? $post->payment_type,
        ]);

        if ($request->filled('location')) {
            $post->locations()->sync([]);
            foreach ($request->input('location', []) as $level => $locationId) {
                $post->locations()->attach($locationId, ['level' => $level]);
            }
        }

        return response()->json(['message' => 'Post updated successfully.'], 200);
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
        $posts = $this->post
            ->select([
                'id',
                'user_id',
                'title',
                'description',
                'price',
                'work_type',
                'payment_type',
                'images',
                'created_at',
                'updated_at',
            ])
            ->with(['locations','user'])
            ->getListByFilter(filter:$filter)
            ->paginate($limit);
        return $this->paginatedResponse(collection: $posts, resourceClass: PostResource::class, limit: $limit,offset: $offset, key:'posts');
    }

    public function getDetails(Request $request):JsonResponse
    {
        $post = $this->post
            ->with(['locations','user'])
            ->where(['id'=> $request['post_id']])
            ->select([
                'id',
                'user_id',
                'title',
                'description',
                'price',
                'work_type',
                'payment_type',
                'images',
                'created_at',
                'updated_at',
            ])->first();
        return response()->json(new PostResource($post));
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
        $checkFavoritePostExist = $this->favoritePost->where(['user_id' => $user->id, 'post_id' => $request['post_id']])->first();
        if ($checkFavoritePostExist)
        {
            return response()->json(['message' => 'Post already favorited'], 422);
        }
        $this->favoritePost->create([
            'post_id' => $request['post_id'],
            'user_id' => $user['id'],
        ]);
        return response()->json(['message' => 'Post added to favorite list.'], 200);
    }

    public function getFavoritePostList(Request $request):array
    {
        $limit =    $request['limit']??10;
        $offset =    $request['offset']??1;
        $this->resolveOffsetPagination(offset: $request['offset']);
        $userId = $request->user()->id;
        $filter = [
            'category_id' => $request['category_id']??null,
            'subcategory_id' => $request['subcategory_id']??null,
            'sub_subcategory_id' => $request['sub_subcategory_id']??null,
            'location'=>$request['location']??null,
        ];
        $posts = $this->post
            ->whereHas('favoritePost', function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->select([
                'id',
                'user_id',
                'title',
                'description',
                'price',
                'work_type',
                'payment_type',
                'images',
                'created_at',
                'updated_at',
            ])
            ->with(['locations','favoritePost'])
            ->getListByFilter(filter:$filter)
            ->paginate($limit);
        return $this->paginatedResponse(collection: $posts, resourceClass: PostResource::class, limit: $limit,offset: $offset, key:'posts');
    }

    public function removeFavorite(Request $request): JsonResponse
    {
        $user = $request->user();

        $favoritePost = $this->favoritePost->where([
            'post_id' => $request['post_id'],
            'user_id' => $user['id'],
        ])->first();

        if (!$favoritePost) {
            return response()->json(['message' => 'Favorite Post not found or unauthorized.'], 404);
        }

        $favoritePost->delete();

        return response()->json(['message' => 'Favorite Post deleted successfully.'], 200);
    }
    public function deletePost(Request $request): JsonResponse
    {
        $user = $request->user();
        $post = $this->post->where([
            'id' => $request['post_id'],
            'user_id' => $user['id'],
        ])->first();
        if (!$post) {
            return response()->json(['message' => 'Post not found or unauthorized.'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully.'], 200);
    }

}
