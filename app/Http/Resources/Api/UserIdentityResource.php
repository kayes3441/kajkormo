<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserIdentityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'user_id' => $this['user_id'],
            'identity_type' => $this['identity_type'],
            'status' => $this['status'],
            'images' => $this['images'],
            'front_image_url' => $this['front_image_url'],
            'back_image_url' => $this['back_image_url'],
            'created_at' => $this['created_at']?->format('Y-m-d H:i:s'),
            'updated_at' => $this['updated_at']?->format('Y-m-d H:i:s'),
            'user' => [
                'id' => $this['user']?->id,
                'name' => $this['user']?->name,
                'email' => $this['user']?->email,
            ],
        ];
    }
}
