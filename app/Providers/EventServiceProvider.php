<?php

namespace App\Providers;

use App\ACS\Events\ParameterLookupDone;
use App\ACS\Events\ProvisionDone;
use App\Listeners\SendLookupParametersToSocket;
use App\Listeners\Webhooks\WebhookAfterProvision;
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

        ParameterLookupDone::class => [
          SendLookupParametersToSocket::class,
        ],

        ProvisionDone::class => [
          WebhookAfterProvision::class,
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
}
