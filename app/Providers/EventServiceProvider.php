<?php

namespace App\Providers;

use App\Events\FetchedDataFailedEvent;
use App\Events\FetchedDataSucceededEvent;
use App\Listeners\SaveDowntimeStats;
use App\Listeners\SendFetchedDataFailedNotification;
use App\Listeners\SendFetchedDataSucceededNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        FetchedDataSucceededEvent::class => [
            SendFetchedDataSucceededNotification::class,
        ],

        FetchedDataFailedEvent::class => [
            SendFetchedDataFailedNotification::class,
        ],

        UptimeCheckRecovered::class => [
            SaveDowntimeStats::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
