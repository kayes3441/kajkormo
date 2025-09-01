<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authId = $request->user()->id;

        return [
            'id'         => $this->id,
            'message'    => $this->message,
            'sender_id'  => $this->sender_id,
            'receiver_id'=> $this->receiver_id,
            'type'       => $this->sender_id == $authId ? 'sent' : 'received',
            'created_at' => $this->created_at->toDateTimeString(),
            'sender'     => [
                'id'   => $this->sender->id ?? null,
                'first_name' => $this->sender->first_name ?? null,
                'last_name' => $this->sender->last_name ?? null,
            ],
            'receiver'   => [
                'id'   => $this->receiver->id ?? null,
                'first_name' => $this->receiver->first_name ?? null,
                'last_name' => $this->receiver->last_name ?? null,
            ],
        ];
    }
}
