<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this['id'],
            'title' => $this['title'],
            'type' => $this['type'],
            'url' => $this['url'],
            'image' => $this['image'],
            'image_url' => $this['image_url'],
        ];
    }
}
