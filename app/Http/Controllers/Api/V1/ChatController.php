<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ChatResource;
use App\Models\Chat;
use App\Trait\PaginatesWithOffsetTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    use PaginatesWithOffsetTrait;
    public function add(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'receiver_id'   => 'required|exists:users,id',
            'message'       => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $files = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $files[] = $file->store('attachments', 'public');
            }
        }

        $chat = Chat::create([
            'sender_id'   => $user['id'],
            'receiver_id' => $request['receiver_id'],
            'message'     => $request['message'],
            'attachments' => $files,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $chat->load('sender:id,first_name,last_name', 'receiver:id,first_name,last_name'),
        ]);
    }

    public function getList(Request $request):JsonResponse
    {
        $user   = $request->user();
        $authId = $user->id;

        $conversations = Chat::with(['sender','receiver'])->where(function($q) use ($authId) {
          return  $q->where('sender_id', $authId);
        })
            ->latest()
            ->get()
            ->groupBy(function($chat) use ($authId) {
                return $chat->sender_id == $authId;
            })
            ->map(function($messages) {
                return $messages->first();
            })
            ->values();
        return response()->json([
            'success' => true,
            'data'    => ChatResource::collection($conversations),
        ]);
    }
    public function getDetails(Request $request): array
    {
        $limit =    $request['limit'] ?? 10;
        $offset =    $request['offset'] ?? 1;
        $this->resolveOffsetPagination(offset: $request['offset']);
        $user   = $request->user();
        $authId = $user->id;

        $receiverId = $request->input('receiver_id');

        $messages = Chat::with(['sender','receiver'])->where(function ($q) use ($authId, $receiverId) {
            $q->where('sender_id', $authId)
                ->where('receiver_id', $receiverId);
        })
            ->orWhere(function ($q) use ($authId, $receiverId) {
                $q->where('sender_id', $receiverId)
                    ->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
        return $this->paginatedResponse(collection: $messages, resourceClass: ChatResource::class, limit: $limit,offset: $offset, key:'data');
    }

    public function read(Request $request):JsonResponse
    {

        $validator = Validator::make($request->all(),[
            'sender_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        Chat::where('sender_id', $request->sender_id)
            ->where('receiver_id', $user['id'])
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read',
        ]);
    }
}
