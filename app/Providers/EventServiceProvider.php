<?php

namespace App\Providers;

use App\Events\FetchedDataFailedEvent;
use App\Events\FetchedDataSucceededEvent;
use App\Listeners\SaveDowntimeStats;
use App\Listeners\SendCertificateCheckFailed;
use App\Listeners\SendCertificateCheckSucceeded;
use App\Listeners\SendCertificateExpiresSoon;
use App\Listeners\SendFetchedDataFailedNotification;
use App\Listeners\SendFetchedDataSucceededNotification;
use App\Listeners\SendUptimeCheckFailed;
use App\Listeners\SendUptimeCheckRecovered;
use App\Listeners\SendUptimeCheckSucceeded;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Spatie\UptimeMonitor\Events\CertificateCheckFailed;
use Spatie\UptimeMonitor\Events\CertificateCheckSucceeded;
use Spatie\UptimeMonitor\Events\CertificateExpiresSoon;
use Spatie\UptimeMonitor\Events\UptimeCheckFailed;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded;

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

        UptimeCheckFailed::class => [
            SendUptimeCheckFailed::class,
        ],

        UptimeCheckSucceeded::class => [
            SendUptimeCheckSucceeded::class,
        ],

        UptimeCheckRecovered::class => [
            SendUptimeCheckRecovered::class,
            SaveDowntimeStats::class,
        ],

        CertificateCheckSucceeded::class => [
            SendCertificateCheckSucceeded::class,
        ],

        CertificateCheckFailed::class => [
            SendCertificateCheckFailed::class,
        ],

        CertificateExpiresSoon::class => [
            SendCertificateExpiresSoon::class,
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
