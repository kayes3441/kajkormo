<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\LocationResource;
use App\Models\Location;
use App\Trait\PaginatesWithOffsetTrait;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use PaginatesWithOffsetTrait;
    public function getList(Request $request):array
    {
        $params = $request['params'];
        $parentID = $request['parent_id'];
        $limit =    $request['limit']??10;
        $offset =    $request['offset']??1;
        $this->resolveOffsetPagination(offset: $request['offset']);
        $locations = Location::select(['id','name','parent_id','level'])
            ->when(isset($params), function ($query) use ($params) {
                return $query->where(['level' => $params]);
            })
            ->when(!is_null($parentID),function ($query) use ($parentID){
                return $query->where(['parent_id'=>$parentID]);
            })->paginate($limit);
        return $this->paginatedResponse(collection: $locations, resourceClass: LocationResource::class, limit: $limit,offset: $offset, key:$params??'locations');
    }
}
