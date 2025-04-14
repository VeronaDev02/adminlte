<?php

namespace App\Providers;

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

    protected $subscribe = [
        // User Auth Listeners
        \App\Listeners\User\Auth\UserAuthEventSubscriber::class,
        
        // User Profile Listeners
        \App\Listeners\User\User\UserEventSubscriber::class,

        // Admin Listeners
        \App\Listeners\Admin\User\UserEventSubscriber::class,
        \App\Listeners\Admin\Role\RoleEventSubscriber::class,
        \App\Listeners\Admin\Selfs\SelfsEventSubscriber::class,
        \App\Listeners\Admin\Unidade\UnidadeEventSubscriber::class,
        \App\Listeners\Admin\Unit\UnitEventSubscriber::class
        
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
