<?php

namespace App\Providers;

use App\Models\CoatingJob;
use App\Models\CoatingJobMarutiItem;
use App\Models\Payment;
use App\Models\PowderAndInventoryLog;
use App\Observers\CoatingJobMarutiItemObserver;
use App\Observers\CoatingJobObserver;
use App\Observers\PaymentObserver;
use App\Observers\PowderAndInventoryLogObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        PowderAndInventoryLog::observe(PowderAndInventoryLogObserver::class);
        CoatingJob::observe(CoatingJobObserver::class);
        CoatingJobMarutiItem::observe(CoatingJobMarutiItemObserver::class);
    }
}
