<?php

namespace App\Providers;

use App\Events\ChattingEvent;
use App\Events\PostVerification;
use App\Listeners\ChattingListener;
use App\Listeners\SendPostVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ChattingEvent::class => [
            ChattingListener::class,
        ],
        PostVerification::class=>[
            SendPostVerificationNotification::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot():void
    {
        parent::boot();
    }
}
