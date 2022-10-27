<?php

namespace Hairavel\Core\Providers;

use Hairavel\Core\Util\Build;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function __construct($app)
    {
        parent::__construct($app);

        // Automatically inject events
        $events = app(Build::class)->getData('events');
        foreach ($events as $event => $listeners) {
            $this->listen[$event] = $listeners;
        }
    }

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //

    }
}
