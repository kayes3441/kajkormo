<?php

namespace App\Listeners;

use App\Events\ChattingEvent;
use App\Events\CustomTopicEvent;
use App\Trait\PushNotificationTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CustomTopicListener
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
    public function handle(CustomTopicEvent $event): void
    {
        $this->sendNotification($event);
    }

    private function sendNotification(CustomTopicEvent $event): void
    {
        $key = $event->key;

        $this->sendCustomTopic(key: $key);
    }
}
