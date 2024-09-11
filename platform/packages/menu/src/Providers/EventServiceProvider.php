<?php

namespace Impiger\Menu\Providers;

use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Menu\Listeners\DeleteMenuNodeListener;
use Impiger\Menu\Listeners\UpdateMenuNodeUrlListener;
use Impiger\Slug\Events\UpdatedSlugEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UpdatedSlugEvent::class    => [
            UpdateMenuNodeUrlListener::class,
        ],
        DeletedContentEvent::class => [
            DeleteMenuNodeListener::class,
        ],
    ];
}
