<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfigureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $value = $this['value'];
        if (in_array($this['key'], ['web_fav_icon', 'app_header_logo']) && $this['value']) {
            $value = asset('storage/' . $this['value']);
        }

        return [
            'key'   => $this['key'],
            'value' => $value,
        ];
    }
}
