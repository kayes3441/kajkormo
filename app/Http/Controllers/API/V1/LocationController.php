<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class LocationController extends Controller
{
    public function getList(Request $request):array
    {
        $params = $request['params'];
        $parentID = $request['parent_id'];
        $currentPage = $request['offset'] ?? Paginator::resolveCurrentPage('page');
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        $locations = Location::select(['id','name','parent_id'])
            ->when(isset($params), function ($query) use ($params) {
                return $query->where(['level' => $params]);
            })
            ->when(!is_null($parentID),function ($query) use ($parentID){
                return $query->where(['parent_id'=>$parentID]);
            })->paginate($request->get('limit',10));;


        return [
            'total_size' => $locations->total(),
            'limit' => (int)$request['limit'],
            'offset' => (int)$request['offset'],
            'locations' => LocationResource::collection($locations)
        ];
    }
}
