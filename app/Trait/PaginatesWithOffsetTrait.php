<?php

namespace App\Trait;

use Illuminate\Pagination\Paginator;

trait PaginatesWithOffsetTrait
{
    public function resolveOffsetPagination($offset):int
    {
        $currentPage = $offset ?? Paginator::resolveCurrentPage('page');
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        return $currentPage;
    }
    public function paginatedResponse($collection, $resourceClass, $limit,$offset, $key = 'data'):array
    {
        return [
            'total_size' => $collection->total(),
            'limit'      => $limit ?? 10,
            'offset'     => $offset,
            $key         => $resourceClass::collection($collection),
        ];
    }
}
