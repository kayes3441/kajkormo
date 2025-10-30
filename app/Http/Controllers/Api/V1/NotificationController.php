<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NotificationResource;
use App\Models\Notification;
use App\Trait\PaginatesWithOffsetTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    use  PaginatesWithOffsetTrait;

    public function getList(Request $request): array
    {
        $userId = $request->user()->id;
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 1;

        $this->resolveOffsetPagination(offset: $offset);

        $notifications = Notification::where(['user_id'=> $userId])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return $this->paginatedResponse(
            collection: $notifications,
            resourceClass: NotificationResource::class,
            limit: $limit,
            offset: $offset,
            key: 'notifications'
        );
    }

    public function bulkAction(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'action' => 'required|in:read,delete',
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'required|integer|exists:notifications,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $userId = $request->user()->id;
        $notifications = Notification::where(['user_id'=> $userId])
            ->whereIn('id', $request['notification_ids']);

        if ($request['action'] === 'read') {
            $notifications->update(['is_read' => true]);
            return response()->json(['message' => 'Notifications marked as read']);
        }

        if ($request['action'] === 'delete') {
            $notifications->delete();
            return response()->json(['message' => 'Notifications deleted']);
        }
    }
}
