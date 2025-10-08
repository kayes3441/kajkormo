<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BannerResource;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Banner;
use App\Trait\PaginatesWithOffsetTrait;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use PaginatesWithOffsetTrait;
    public function getList(Request $request):array
    {
        $params = $request['params'];
        $limit =    $request['limit'] ?? 10;
        $offset =    $request['offset'] ?? 1;
        $this->resolveOffsetPagination(offset: $request['offset']);
        $banners = Banner::select(['id','title','type', 'url', 'image','status',])
            ->when(isset($params), function ($query) use ($params) {
                return $query->where(['type' => $params]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
        return $this->paginatedResponse(collection: $banners, resourceClass: BannerResource::class, limit: $limit,offset: $offset, key:'banners');
    }
}
