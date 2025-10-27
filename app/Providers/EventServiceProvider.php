<?php

namespace App\Providers;

use App\Events\ChattingEvent;
use App\Listeners\ChattingListener;
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
