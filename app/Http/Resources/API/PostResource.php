<?php

namespace App\Http\Resources\API;

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
            'user_id'           => $this['user_id'],
            'title'             => $this['title'],
            'description'       => $this['description'],
            'price'             => $this['price'],
            'work_type'         => $this['work_type'],
            'payment_type'      => $this['payment_type'],
            'locations' => LocationResource::collection($this->whenLoaded('locations')),
        ];
    }
}
