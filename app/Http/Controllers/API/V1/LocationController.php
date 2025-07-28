<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
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
        $locations = Location::select(['id','name','parent_id'])->where(['level'=>$params])
            ->when(!is_null($parentID),function ($query) use ($parentID){
                return $query->where(['parent_id'=>$parentID]);
            })->get();
        $currentPage = $request['offset'] ?? Paginator::resolveCurrentPage('page');
        $locations = new LengthAwarePaginator($locations, $locations->count(), $request->get('limit', 10), $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
            'appends' => $request->all(),
        ]);

        return [
            'total_size' => $locations->total(),
            'limit' => (int)$request['limit'],
            'offset' => (int)$request['offset'],
            'locations' => $locations->values()
        ];
    }
}
