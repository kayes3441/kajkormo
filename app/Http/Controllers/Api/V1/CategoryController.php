<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use App\Trait\PaginatesWithOffsetTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use PaginatesWithOffsetTrait;
    public function getList(Request $request):array
    {
        $params = $request['params'];
        $parentID = $request['parent_id'];
        $limit =    $request['limit'] ?? 10;
        $offset =    $request['offset'] ?? 1;
        $this->resolveOffsetPagination(offset: $request['offset']);
        $categories = Category::select(['id','name','parent_id','slug','level','icon'])
            ->when(isset($params), function ($query) use ($params) {
                return $query->where(['level' => $params]);
            })
            ->when(!is_null($parentID), function ($query) use ($parentID) {
                return $query->where(['parent_id' => $parentID]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
        return $this->paginatedResponse(collection: $categories, resourceClass: CategoryResource::class, limit: $limit,offset: $offset, key:$params??'categories');
    }
}
