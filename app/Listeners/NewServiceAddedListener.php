<?php

namespace App\Listeners;

use App\Events\ChattingEvent;
use App\Events\NewServiceAddedTopicEvent;
use App\Trait\PushNotificationTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewServiceAddedListener
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
    public function handle(NewServiceAddedTopicEvent $event): void
    {
        $this->sendNotification($event);
    }

    private function sendNotification(NewServiceAddedTopicEvent $event): void
    {
        $key = $event->key;
        $categoryName = $event->categoryName;
        $locationName = $event->locationName;
        $topic = $event->topic;
        $this->sendNewServiceAddedTopic(key: $key, topic: $topic,categoryName: $categoryName, locationName: $locationName);
    }
}
