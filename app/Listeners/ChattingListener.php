<?php

namespace App\Listeners;

use App\Events\ChattingEvent;
use App\Trait\PushNotificationTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ChattingListener
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
    public function handle(ChattingEvent $event): void
    {
        $this->sendNotification($event);
    }

    private function sendNotification(ChattingEvent $event): void
    {
        $key = $event->key;
        $receiverData = $event->receiverData;
        $messageForm = $event->messageForm;
        $this->chattingNotification(key: $key, receiverData: $receiverData, messageForm: $messageForm);
    }
}
