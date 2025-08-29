<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id'   => 'required|exists:users,id',
            'message'       => 'nullable|string',
            'attachments.*' => 'nullable|file|max:5120', // 5MB each
        ]);

        $files = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $files[] = $file->store('attachments', 'public');
            }
        }

        $chat = Chat::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
            'attachments' => $files,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $chat->load('sender:id,name', 'receiver:id,name'),
        ]);
    }


    public function getList(Request $request):JsonResponse
    {
        $authId = auth()->id();

        $conversations = Chat::where(function($q) use ($authId) {
            $q->where('sender_id', $authId)
                ->orWhere('receiver_id', $authId);
        })
            ->latest()
            ->get()
            ->groupBy(function($chat) use ($authId) {
                return $chat->sender_id == $authId ? $chat->receiver_id : $chat->sender_id;
            })
            ->map(function($messages) {
                return $messages->first();
            })
            ->values();

        return response()->json([
            'success' => true,
            'data'    => $conversations,
        ]);
    }
    public function read(Request $request):JsonResponse
    {
        $request->validate([
            'sender_id' => 'required|exists:users,id',
        ]);

        Chat::where('sender_id', $request->sender_id)
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read',
        ]);
    }
}
