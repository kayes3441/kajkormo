<?php

namespace App\Listeners;

use App\Events\PostVerification;
use App\Trait\PushNotificationTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPostVerificationNotification
{
    use PushNotificationTrait;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostVerification $event): void
    {
        $this->sendNotification($event);
    }

    private function sendNotification(PostVerification $event): void
    {
        $key = $event->key;
        $userData = $event->userData;
        $this->postVerificationNotification(key: $key,userData:$userData);
    }
}
