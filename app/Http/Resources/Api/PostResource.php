<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this['id'],
            'user_id'           => $this->when(!($this['user_id'] === null), $this['user_id']),
            'title'             => $this['title'],
            'description'       => $this['description'],
            'price'             => $this['price'],
            'work_type'         => $this['work_type'],
            'payment_type'      => $this['payment_type'],
            'average_rating'      => $this['average_rating'],
            'locations' => $this->whenLoaded('locations'),
            'user'     => UserResource::make($this->whenLoaded('user')),
            'reviews' => $this->whenLoaded('reviews', function () {
                return $this->reviews->isNotEmpty()
                    ? ReviewPostResource::collection($this->reviews)
                    : null;
            }),
        ];
    }
    protected function shouldShowPostFields()
    {
        return $this->showPostFields ?? false;
    }
}
