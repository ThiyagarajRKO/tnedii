<?php

namespace Impiger\ACL\Providers;

use Impiger\ACL\Events\RoleAssignmentEvent;
use Impiger\ACL\Events\RoleUpdateEvent;
use Impiger\ACL\Listeners\LoginListener;
use Impiger\ACL\Listeners\RoleAssignmentListener;
use Impiger\ACL\Listeners\RoleUpdateListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        RoleUpdateEvent::class     => [
            RoleUpdateListener::class,
        ],
        RoleAssignmentEvent::class => [
            RoleAssignmentListener::class,
        ],
        Login::class               => [
            LoginListener::class,
        ],
    ];
}
