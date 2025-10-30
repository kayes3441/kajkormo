<?php

namespace App\Trait;

use App\Models\Notification;

trait NotifiableStorageTrait
{
    /**
     * Store a notification for a user
     *
     * @param  string  $title
     * @param  string  $message
     */
    public function storeNotification($user, string $title, string $message):void
    {
        $userId = is_object($user) ? $user->id : $user;

        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
        ]);

    }
}
