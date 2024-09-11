<?php

namespace Impiger\AuditLog\Providers;

use Impiger\AuditLog\Events\AuditHandlerEvent;
use Impiger\AuditLog\Listeners\AuditHandlerListener;
use Impiger\AuditLog\Listeners\CreatedContentListener;
use Impiger\AuditLog\Listeners\DeletedContentListener;
use Impiger\AuditLog\Listeners\LoginListener;
use Impiger\AuditLog\Listeners\UpdatedContentListener;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
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
        AuditHandlerEvent::class   => [
            AuditHandlerListener::class,
        ],
        Login::class               => [
            LoginListener::class,
        ],
        UpdatedContentEvent::class => [
            UpdatedContentListener::class,
        ],
        CreatedContentEvent::class => [
            CreatedContentListener::class,
        ],
        DeletedContentEvent::class => [
            DeletedContentListener::class,
        ],
    ];
}
