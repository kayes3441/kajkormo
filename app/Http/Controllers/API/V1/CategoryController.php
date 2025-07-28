<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class CategoryController extends Controller
{
    public function getList(Request $request):array
    {
        $params = $request['params'];
        $parentID = $request['parent_id'];
        $categories = Category::select(['id','name','parent_id','slug','level'])->where(['level'=>$params])
            ->when(!is_null($parentID),function ($query) use ($parentID){
                return $query->where(['parent_id'=>$parentID]);
            })->get();
        $currentPage = $request['offset'] ?? Paginator::resolveCurrentPage('page');
        $categories = new LengthAwarePaginator($categories, $categories->count(), $request->get('limit', 10), $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
            'appends' => $request->all(),
        ]);

        return [
            'total_size' => $categories->total(),
            'limit' => (int)$request['limit'],
            'offset' => (int)$request['offset'],
            $params => $categories->values()
        ];
    }
}
